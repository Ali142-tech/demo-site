<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'tagline', 'description', 'features', 'options',
        'starting_price', 'price_unit', 'min_quantity', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'options' => 'array',
            'is_active' => 'boolean',
            'starting_price' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }
}
