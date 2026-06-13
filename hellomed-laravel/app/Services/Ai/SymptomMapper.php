<?php

namespace App\Services\Ai;

/**
 * Maps common patient symptom phrases → medical department/specialty keywords.
 * Used by ContextBuilder to find relevant DB records even before the LLM runs.
 */
class SymptomMapper
{
    /**
     * Symptom phrase → array of department/specialty keywords to search.
     *
     * @var array<string, array<string>>
     */
    private static array $map = [
        // ── Cardiology ─────────────────────────────────────────
        'chest pain'         => ['cardiology', 'heart', 'cardiac'],
        'chest tightness'    => ['cardiology', 'heart'],
        'heart attack'       => ['cardiology', 'cardiac', 'emergency'],
        'palpitations'       => ['cardiology', 'heart'],
        'breathlessness'     => ['cardiology', 'pulmonology', 'lung'],
        'shortness of breath'=> ['cardiology', 'pulmonology'],
        'high blood pressure'=> ['cardiology', 'hypertension'],
        'hypertension'       => ['cardiology'],
        'heart failure'      => ['cardiology', 'cardiac'],
        'irregular heartbeat'=> ['cardiology'],
        'arrhythmia'         => ['cardiology'],
        'edema'              => ['cardiology', 'kidney'],
        'swollen legs'       => ['cardiology', 'kidney'],

        // ── Orthopedics ────────────────────────────────────────
        'fracture'           => ['orthopedics', 'bone'],
        'broken bone'        => ['orthopedics', 'bone'],
        'joint pain'         => ['orthopedics', 'rheumatology'],
        'knee pain'          => ['orthopedics', 'joint'],
        'back pain'          => ['orthopedics', 'spine'],
        'lower back pain'    => ['orthopedics', 'spine'],
        'neck pain'          => ['orthopedics', 'spine'],
        'shoulder pain'      => ['orthopedics'],
        'hip pain'           => ['orthopedics'],
        'arthritis'          => ['orthopedics', 'rheumatology'],
        'scoliosis'          => ['orthopedics', 'spine'],
        'sports injury'      => ['orthopedics'],
        'sprain'             => ['orthopedics'],
        'ligament'           => ['orthopedics'],
        'bone density'       => ['orthopedics'],

        // ── Dental ─────────────────────────────────────────────
        'toothache'          => ['dental', 'dentistry'],
        'tooth pain'         => ['dental', 'dentistry'],
        'gum bleeding'       => ['dental', 'dentistry', 'gum'],
        'cavity'             => ['dental', 'dentistry'],
        'root canal'         => ['dental', 'dentistry'],
        'wisdom tooth'       => ['dental', 'dentistry'],
        'braces'             => ['dental', 'orthodontics'],
        'bad breath'         => ['dental'],
        'gum disease'        => ['dental', 'periodontics'],
        'mouth sore'         => ['dental'],
        'jaw pain'           => ['dental', 'orthodontics'],

        // ── Psychiatry / Mental Health ──────────────────────────
        'anxiety'            => ['psychiatry', 'mental health'],
        'depression'         => ['psychiatry', 'mental health'],
        'panic attack'       => ['psychiatry', 'mental health'],
        'stress'             => ['psychiatry', 'mental health'],
        'insomnia'           => ['psychiatry', 'sleep', 'mental health'],
        'sleep disorder'     => ['psychiatry', 'sleep'],
        'bipolar'            => ['psychiatry'],
        'schizophrenia'      => ['psychiatry'],
        'ocd'                => ['psychiatry'],
        'ptsd'               => ['psychiatry'],
        'addiction'          => ['psychiatry', 'rehabilitation'],
        'eating disorder'    => ['psychiatry', 'nutrition'],
        'mood swings'        => ['psychiatry'],
        'phobia'             => ['psychiatry'],

        // ── General / Internal Medicine ────────────────────────
        'fever'              => ['general', 'internal medicine'],
        'cold'               => ['general', 'internal medicine'],
        'flu'                => ['general', 'internal medicine'],
        'fatigue'            => ['general', 'internal medicine'],
        'headache'           => ['general', 'neurology'],
        'migraine'           => ['general', 'neurology'],
        'nausea'             => ['general', 'gastroenterology'],
        'vomiting'           => ['general', 'gastroenterology'],
        'diarrhea'           => ['general', 'gastroenterology'],
        'stomach pain'       => ['general', 'gastroenterology'],
        'abdominal pain'     => ['general', 'gastroenterology'],
        'weight loss'        => ['general', 'endocrinology'],
        'diabetes'           => ['general', 'endocrinology'],
        'thyroid'            => ['endocrinology'],
        'skin rash'          => ['dermatology'],
        'allergy'            => ['general', 'allergy'],
        'urinary'            => ['urology', 'nephrology'],
        'kidney'             => ['nephrology', 'urology'],
        'eye pain'           => ['ophthalmology'],
        'vision'             => ['ophthalmology'],
        'ear pain'           => ['ent', 'otolaryngology'],
        'hearing'            => ['ent', 'otolaryngology'],

        // ── Nutrition & Dietetics ───────────────────────────────────
        'underweight'        => ['nutrition', 'dietetics', 'diet'],
        'overweight'         => ['nutrition', 'dietetics', 'diet', 'endocrinology'],
        'weight gain'        => ['nutrition', 'dietetics', 'diet'],
        'weight loss'        => ['nutrition', 'dietetics', 'diet', 'endocrinology'],
        'obesity'            => ['nutrition', 'dietetics', 'diet', 'endocrinology'],
        'malnutrition'       => ['nutrition', 'dietetics', 'diet'],
        'diet'               => ['nutrition', 'dietetics'],
        'nutrition'          => ['nutrition', 'dietetics'],
        'bmi'                => ['nutrition', 'dietetics'],
        'vitamin deficiency' => ['nutrition', 'dietetics'],
        'anemia'             => ['nutrition', 'dietetics', 'general'],
        'cholesterol'        => ['nutrition', 'dietetics', 'cardiology'],
        'fatty liver'        => ['nutrition', 'dietetics', 'gastroenterology'],
        'eating habit'       => ['nutrition', 'dietetics'],
        'meal plan'          => ['nutrition', 'dietetics'],

        // ── Pediatrics ────────────────────────────────────────
        'child'              => ['pediatrics', 'child health'],
        'baby'               => ['pediatrics'],
        'infant'             => ['pediatrics'],
        'toddler'            => ['pediatrics'],
        'kid'                => ['pediatrics'],
        'vaccination'        => ['pediatrics'],
        'growth delay'       => ['pediatrics', 'endocrinology'],

        // ── Neurology ────────────────────────────────────────────
        'seizure'            => ['neurology'],
        'epilepsy'           => ['neurology'],
        'numbness'           => ['neurology', 'orthopedics'],
        'tremor'             => ['neurology'],
        'memory loss'        => ['neurology', 'psychiatry'],
        'dementia'           => ['neurology', 'psychiatry'],
        'paralysis'          => ['neurology', 'emergency'],
        'dizziness'          => ['neurology', 'general'],
        'vertigo'            => ['neurology', 'ent'],

        // ── Dermatology ───────────────────────────────────────
        'acne'               => ['dermatology'],
        'eczema'             => ['dermatology'],
        'psoriasis'          => ['dermatology'],
        'hair loss'          => ['dermatology'],
        'itching'            => ['dermatology', 'allergy'],
        'rash'               => ['dermatology'],
        'hives'              => ['dermatology', 'allergy'],
        'skin'               => ['dermatology'],

        // ── Oncology ────────────────────────────────────────────
        'cancer'             => ['oncology'],
        'tumor'              => ['oncology'],
        'chemotherapy'       => ['oncology'],
        'lymphoma'           => ['oncology'],
        'leukemia'           => ['oncology'],
        'biopsy'             => ['oncology'],

        // ── Emergency ────────────────────────────────────────────
        'emergency'          => ['emergency'],
        'accident'           => ['emergency', 'orthopedics'],
        'stroke'             => ['emergency', 'neurology'],
        'unconscious'        => ['emergency'],
        'bleeding'           => ['emergency'],
        'burn'               => ['emergency', 'dermatology'],
    ];

    /**
     * Stop words to ignore when tokenizing user input.
     *
     * @var array<string>
     */
    private static array $stopWords = [
        'i', 'me', 'my', 'have', 'has', 'had', 'am', 'is', 'are', 'was', 'were',
        'a', 'an', 'the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for',
        'of', 'with', 'by', 'from', 'some', 'very', 'really', 'quite', 'feel',
        'feeling', 'experiencing', 'suffering', 'problem', 'issue', 'been',
        'little', 'bit', 'lot', 'please', 'help', 'can', 'you', 'get', 'what',
    ];

    /**
     * Extracts relevant department/specialty keywords from a patient's message.
     *
     * @return array<string>
     */
    public static function extractKeywords(string $message): array
    {
        $lower    = strtolower($message);
        $keywords = [];

        // 1. Multi-word phrase matching (highest priority)
        foreach (self::$map as $phrase => $departments) {
            if (str_contains($lower, $phrase)) {
                $keywords = array_merge($keywords, $departments);
            }
        }

        // 2. Single-word token matching (fallback)
        if (empty($keywords)) {
            $tokens = preg_split('/\W+/', $lower);
            foreach ($tokens as $token) {
                if (strlen($token) < 3 || in_array($token, self::$stopWords)) {
                    continue;
                }
                if (isset(self::$map[$token])) {
                    $keywords = array_merge($keywords, self::$map[$token]);
                }
            }
        }

        return array_values(array_unique($keywords));
    }

    /**
     * Returns meaningful search tokens (non-stop words with min length 3).
     *
     * @return array<string>
     */
    public static function tokenize(string $message): array
    {
        $tokens = preg_split('/\W+/', strtolower($message));

        return array_values(array_filter($tokens, function (string $token): bool {
            return strlen($token) >= 3 && ! in_array($token, self::$stopWords);
        }));
    }

    /**
     * Detects if the message is likely a website how-to / navigation question.
     */
    public static function isHowToQuery(string $message): bool
    {
        $lower = strtolower($message);

        $patterns = [
            'how do i', 'how to', 'how can i', 'where can i', 'where do i',
            'how do i book', 'how to book', 'book appointment', 'how to order',
            'how to buy', 'how to pay', 'how to register', 'how to login',
            'where is', 'where are', 'find my', 'see my', 'view my',
            'my prescription', 'my appointment', 'my order', 'my record',
            'what is hellomed', 'what can i', 'can i book', 'can i order',
            'how do i get', 'how do i see', 'how do i find', 'how do i access',
            'download my', 'download prescription', 'download result',
            'change my', 'update my', 'edit my profile',
            'call ambulance', 'request ambulance',
            'upload', 'attach', 'submit', 'navigate',
            'step by step', 'guide me',
            'tell me about the website',
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($lower, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detects if the message is a general medical information query
     * ("what is X?", "what does X mean?", "explain X") rather than a symptom report.
     * These queries should search tests/articles by the term directly.
     */
    public static function isInfoQuery(string $message): bool
    {
        $lower = strtolower(trim($message));

        $patterns = [
            'what is a ', 'what is an ', 'what is the ', 'what is ',
            'what are ', 'what does ', 'what do ',
            'explain ', 'define ', 'tell me about ',
            'what test', 'what kind of test', 'what type of test',
            'what means', 'meaning of',
        ];

        foreach ($patterns as $pattern) {
            if (str_starts_with($lower, $pattern) || str_contains($lower, $pattern)) {
                // Extra check: not about the website
                if (! self::isHowToQuery($message)) {
                    return true;
                }
            }
        }

        return false;
    }
}
