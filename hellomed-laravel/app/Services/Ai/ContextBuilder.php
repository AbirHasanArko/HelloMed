<?php

namespace App\Services\Ai;

use App\Models\Article;
use App\Models\AvailableTest;
use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Support\Facades\Storage;

/**
 * Queries the MySQL database to fetch relevant doctors, articles, departments,
 * and diagnostic tests based on the patient's message.
 * Results are returned as arrays ready to be embedded into the LLM's system prompt.
 */
class ContextBuilder
{
    private int $maxDoctors;
    private int $maxArticles;
    private int $maxTests;

    public function __construct()
    {
        $this->maxDoctors  = config('ai.context.max_doctors', 10);
        $this->maxArticles = config('ai.context.max_articles', 4);
        $this->maxTests    = config('ai.context.max_tests', 4);
    }

    /**
     * Build the full context payload from the patient's message.
     * (Legacy — kept as fallback. Main pipeline now uses buildForDepartments.)
     *
     * @return array<string, mixed>
     */
    public function build(string $message): array
    {
        $keywords    = SymptomMapper::extractKeywords($message);
        $tokens      = SymptomMapper::tokenize($message);
        $searchTerms = array_unique(array_merge($keywords, $tokens));

        return [
            'doctors'     => $this->getAllDoctors(),
            'articles'    => $this->findArticles($searchTerms),
            'tests'       => $this->findTests($searchTerms),
            'departments' => $this->getAllDepartments(),
            'keywords'    => $searchTerms,
        ];
    }

    /**
     * Return just the department names (for Stage 1 classification prompt).
     *
     * @return array<string>
     */
    public function getDepartmentNames(): array
    {
        return Department::where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    /**
     * Stage 2 of the two-stage pipeline:
     * Fetch doctors from specific departments chosen by the LLM in Stage 1,
     * plus keyword-filtered articles and tests.
     *
     * @param  string         $message      Original patient message (for article/test search)
     * @param  array<string>  $deptNames    Department names chosen by Stage 1 LLM
     * @return array<string, mixed>
     */
    public function buildForDepartments(string $message, array $deptNames): array
    {
        $keywords    = SymptomMapper::extractKeywords($message);
        $tokens      = SymptomMapper::tokenize($message);
        $searchTerms = array_unique(array_merge($keywords, $tokens));

        // If no valid departments were picked, fall back to all doctors
        if (empty($deptNames)) {
            $doctors = $this->getAllDoctors();
        } else {
            $doctors = Doctor::with('department')
                ->where('is_active', true)
                ->whereHas('department', fn ($q) => $q->whereIn('name', $deptNames))
                ->orderByDesc('is_featured')
                ->orderByDesc('experience_years')
                ->get()
                ->map(fn (Doctor $d) => $this->formatDoctor($d))
                ->toArray();
        }

        return [
            'doctors'     => $doctors,
            'articles'    => $this->findArticles(array_merge($searchTerms, $this->deptTerms($deptNames))),
            'tests'       => $this->findTests($searchTerms),
            'departments' => $this->getAllDepartments(),
        ];
    }

    /**
     * Extract searchable terms from department names for article search broadening.
     *
     * @param  array<string>  $deptNames
     * @return array<string>
     */
    private function deptTerms(array $deptNames): array
    {
        $terms = [];
        foreach ($deptNames as $name) {
            foreach (explode(' ', strtolower($name)) as $word) {
                if (strlen($word) > 3 && ! in_array($word, ['and', 'the', 'for', 'with'], true)) {
                    $terms[] = $word;
                }
            }
        }
        return $terms;
    }

    /**
     * Build context for "What is X?" information queries.
     * Searches tests and articles directly by message terms — no department needed.
     *
     * @return array<string, mixed>
     */
    public function buildForInfo(string $message): array
    {
        $tokens = SymptomMapper::tokenize($message);

        // Also extract the quoted/proper noun from the message
        // e.g. "what is Total IGE test?" → terms include 'ige', 'total', 'test'
        preg_match_all('/\b[A-Z]{2,}\b/', $message, $acronyms);
        $extraTerms = array_map('strtolower', $acronyms[0] ?? []);
        $searchTerms = array_unique(array_merge($tokens, $extraTerms));

        return [
            'doctors'     => [],
            'articles'    => $this->findArticles($searchTerms),
            'tests'       => $this->findTestsForInfo($searchTerms, $message),
            'departments' => [],
        ];
    }

    /**
     * Search tests for info queries — uses broader matching including the raw message.
     *
     * @param  array<string>  $terms
     * @return array<array<string, mixed>>
     */
    private function findTestsForInfo(array $terms, string $rawMessage): array
    {
        if (empty($terms)) {
            return [];
        }

        $query = AvailableTest::where('is_active', true);
        $query->where(function ($q) use ($terms, $rawMessage): void {
            foreach ($terms as $term) {
                $like = "%{$term}%";
                $q->orWhere('name', 'LIKE', $like)
                  ->orWhere('description', 'LIKE', $like);
            }
            // Also search with the full raw message (helps for acronyms like "IGE", "CBC")
            $q->orWhere('name', 'LIKE', '%' . $rawMessage . '%');
        });

        return $query
            ->limit($this->maxTests * 2) // Allow more results for info queries
            ->get()
            ->map(fn (AvailableTest $t) => [
                'id'          => $t->id,
                'name'        => $t->name,
                'description' => $t->description,
                'fee'         => $t->fee_bdt,
                'location'    => $t->location,
                'room'        => $t->lab_room_number,
                'url'         => url("/diagnostic-services/{$t->slug}"),
            ])
            ->toArray();
    }

    /**
     * Returns ALL active doctors ordered by featured status.
     * (Used as fallback when department classification returns nothing.)
     *
     * @return array<array<string, mixed>>
     */
    private function getAllDoctors(): array
    {
        return Doctor::with('department')
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderByDesc('experience_years')
            ->get()
            ->map(fn (Doctor $d) => $this->formatDoctor($d))
            ->toArray();
    }

    /**
     * Find relevant published articles by keyword matching title, excerpt, and body.
     *
     * @param  array<string>  $terms
     * @return array<array<string, mixed>>
     */
    private function findArticles(array $terms): array
    {
        if (empty($terms)) {
            return [];
        }

        $query = Article::where('is_published', true);

        $query->where(function ($q) use ($terms): void {
            foreach ($terms as $term) {
                $like = "%{$term}%";
                $q->orWhere('title', 'LIKE', $like)
                  ->orWhere('excerpt', 'LIKE', $like)
                  ->orWhere('body', 'LIKE', $like);
            }
        });

        return $query
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit($this->maxArticles)
            ->get()
            ->map(fn (Article $a) => [
                'id'          => $a->id,
                'title'       => $a->title,
                'excerpt'     => $a->excerpt ?? substr(strip_tags($a->body ?? ''), 0, 120) . '...',
                'url'         => url("/articles/{$a->slug}"),
                'cover_image' => $a->cover_image_path ? Storage::url($a->cover_image_path) : null,
            ])
            ->toArray();
    }

    /**
     * Find relevant active diagnostic tests by name and description.
     *
     * @param  array<string>  $terms
     * @return array<array<string, mixed>>
     */
    private function findTests(array $terms): array
    {
        if (empty($terms)) {
            return [];
        }

        $query = AvailableTest::where('is_active', true);

        $query->where(function ($q) use ($terms): void {
            foreach ($terms as $term) {
                $like = "%{$term}%";
                $q->orWhere('name', 'LIKE', $like)
                  ->orWhere('description', 'LIKE', $like);
            }
        });

        return $query
            ->limit($this->maxTests)
            ->get()
            ->map(fn (AvailableTest $t) => [
                'id'          => $t->id,
                'name'        => $t->name,
                'description' => $t->description,
                'fee'         => $t->fee_bdt,
                'location'    => $t->location,
                'room'        => $t->lab_room_number,
                'url'         => url("/diagnostic-services/{$t->slug}"),
            ])
            ->toArray();
    }

    /**
     * Get all active departments (used in system prompt context).
     *
     * @return array<array<string, mixed>>
     */
    private function getAllDepartments(): array
    {
        return Department::where('is_active', true)
            ->orderByDesc('is_featured')
            ->get()
            ->map(fn (Department $d) => [
                'id'          => $d->id,
                'name'        => $d->name,
                'description' => $d->description,
                'url'         => url("/departments/{$d->slug}"),
            ])
            ->toArray();
    }

    /**
     * Returns featured/active doctors as a fallback when no keyword match is found.
     *
     * @return array<array<string, mixed>>
     */
    private function getFeaturedDoctors(): array
    {
        return Doctor::with('department')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderByDesc('experience_years')
            ->limit($this->maxDoctors)
            ->get()
            ->map(fn (Doctor $d) => $this->formatDoctor($d))
            ->toArray();
    }

    /**
     * Serialise a Doctor model into the array shape the LLM prompt expects.
     *
     * @return array<string, mixed>
     */
    private function formatDoctor(Doctor $doctor): array
    {
        return [
            'id'              => $doctor->id,
            'name'            => $doctor->name,
            'slug'            => $doctor->slug,
            'specialty'       => $doctor->specialty,
            'department'      => $doctor->department?->name,
            'qualification'   => $doctor->qualification,
            'experience_years'=> $doctor->experience_years,
            'online_fee'      => $doctor->online_fee,
            'offline_fee'     => $doctor->offline_fee,
            'online_available'=> $doctor->online_available,
            'offline_available'=> $doctor->offline_available,
            'photo_url'       => $doctor->photo_path ? Storage::url($doctor->photo_path) : null,
            'booking_url'     => url("/appointments/create/{$doctor->slug}"),
            'profile_url'     => url("/doctors/{$doctor->slug}"),
        ];
    }
}
