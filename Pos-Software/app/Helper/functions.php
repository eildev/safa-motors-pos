<?php

namespace App\Helper;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

if (!function_exists('generateUniqueSlug')) {
    /**
     * Generate a unique slug for a given model and column.
     *
     * @param string $name The base name for the slug.
     * @param Model $model The Eloquent model to check against.
     * @param string $column The column to check for uniqueness (default: 'slug').
     * @return string The unique slug.
     */
    function generateUniqueSlug(string $name, Model $model, string $column = 'slug'): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($model->where($column, $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
