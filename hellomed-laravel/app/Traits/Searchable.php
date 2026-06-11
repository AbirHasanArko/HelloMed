<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Scope a query to search across searchable fields.
     *
     * @param Builder $query
     * @param string|null $term
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term) || empty($this->searchableFields)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            foreach ($this->searchableFields as $index => $field) {
                // Handle nested relations like "patientProfile.phone"
                if (str_contains($field, '.')) {
                    $relations = explode('.', $field);
                    $column = array_pop($relations);
                    $relationPath = implode('.', $relations);

                    $q->orWhereHas($relationPath, function (Builder $relQuery) use ($column, $term) {
                        $relQuery->where($column, 'like', '%' . $term . '%');
                    });
                } else {
                    if ($index === 0) {
                        $q->where($field, 'like', '%' . $term . '%');
                    } else {
                        $q->orWhere($field, 'like', '%' . $term . '%');
                    }
                }
            }
        });
    }

    /**
     * Scope a query to apply key-value filters.
     *
     * @param Builder $query
     * @param array|null $filters
     * @return Builder
     */
    public function scopeFilter(Builder $query, ?array $filters): Builder
    {
        if (empty($filters)) {
            return $query;
        }

        foreach ($filters as $key => $value) {
            if (blank($value)) {
                continue;
            }

            // If a custom filter method exists, use it (e.g., filterStatus($query, $value))
            $customMethod = 'filter' . str($key)->studly();
            if (method_exists($this, $customMethod)) {
                $this->$customMethod($query, $value);
                continue;
            }

            // Handle date ranges if value is an array ['start' => '...', 'end' => '...']
            if (is_array($value) && (isset($value['start']) || isset($value['end']))) {
                if (!empty($value['start'])) {
                    $query->whereDate($key, '>=', $value['start']);
                }
                if (!empty($value['end'])) {
                    $query->whereDate($key, '<=', $value['end']);
                }
                continue;
            }

            // Handle nested relationship filters "patientProfile.gender" => "Male"
            if (str_contains($key, '.')) {
                $relations = explode('.', $key);
                $column = array_pop($relations);
                $relationPath = implode('.', $relations);

                $query->whereHas($relationPath, function (Builder $relQuery) use ($column, $value) {
                    if (is_array($value)) {
                        $relQuery->whereIn($column, $value);
                    } else {
                        $relQuery->where($column, $value);
                    }
                });
                continue;
            }

            // Standard exact match or IN
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query;
    }

    /**
     * Helper to apply search, filters, and handle suggest JSON response.
     */
    public static function handleSearchAndFilters(\Illuminate\Http\Request $request, $query = null, ?\Closure $suggestMap = null)
    {
        $query = $query ?? static::query();

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('filters')) {
            $query->filter($request->filters);
        }

        if ($request->boolean('suggest')) {
            $suggestions = $query->take(5)->get()->map($suggestMap ?? function ($model) {
                return [
                    'id' => $model->id,
                    'title' => $model->name ?? $model->title ?? ('#' . $model->id),
                    'subtitle' => ''
                ];
            });
            return response()->json($suggestions);
        }

        return $query;
    }
}
