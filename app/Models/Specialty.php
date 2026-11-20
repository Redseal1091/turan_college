<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'slug',
        'qualification',
        'department',
        'base_education',
        'duration',
        'study_form',
        'description',
        'skills',
        'career_prospects',
        'tuition_fee',
        'grants_count',
        'icon_name',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'skills' => 'array',
        'career_prospects' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'tuition_fee' => 'integer',
        'grants_count' => 'integer',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
