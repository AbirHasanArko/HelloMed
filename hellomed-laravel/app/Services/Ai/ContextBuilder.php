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
        $this->maxDoctors  = config('ai.context.max_doctors', 5);
        $this->maxArticles = config('ai.context.max_articles', 4);
        $this->maxTests    = config('ai.context.max_tests', 4);
    }

    /**
     * Build the full context payload from the patient's message.
     *
     * @return array{
     *   doctors: array,
     *   articles: array,
     *   tests: array,
     *   departments: array,
     *   keywords: array<string>,
     * }
     */
    public function build(string $message): array
    {
        $keywords    = SymptomMapper::extractKeywords($message);
        $tokens      = SymptomMapper::tokenize($message);
        $searchTerms = array_unique(array_merge($keywords, $tokens));

        return [
            'doctors'     => $this->findDoctors($searchTerms),
            'articles'    => $this->findArticles($searchTerms),
            'tests'       => $this->findTests($searchTerms),
            'departments' => $this->getAllDepartments(),
            'keywords'    => $searchTerms,
        ];
    }

    /**
     * Find relevant doctors using keyword matching across name, specialty,
     * bio, and department name. Ranked by featured status, experience, reviews.
     *
     * @param  array<string>  $terms
     * @return array<array<string, mixed>>
     */
    private function findDoctors(array $terms): array
    {
        if (empty($terms)) {
            return $this->getFeaturedDoctors();
        }

        $query = Doctor::with('department')
            ->where('is_active', true);

        $query->where(function ($q) use ($terms): void {
            foreach ($terms as $term) {
                $like = "%{$term}%";
                $q->orWhere('name', 'LIKE', $like)
                  ->orWhere('specialty', 'LIKE', $like)
                  ->orWhere('bio', 'LIKE', $like)
                  ->orWhere('qualification', 'LIKE', $like)
                  ->orWhereHas('department', fn ($d) => $d->where('name', 'LIKE', $like)
                      ->orWhere('description', 'LIKE', $like)
                      ->orWhere('service_scope', 'LIKE', $like));
            }
        });

        $doctors = $query
            ->orderByDesc('is_featured')
            ->orderByDesc('experience_years')
            ->limit($this->maxDoctors)
            ->get();

        // Fall back to featured doctors if nothing matched
        if ($doctors->isEmpty()) {
            return $this->getFeaturedDoctors();
        }

        return $doctors->map(fn (Doctor $d) => $this->formatDoctor($d))->toArray();
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
