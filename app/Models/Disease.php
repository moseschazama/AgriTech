<?php
// app/Models/Disease.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Disease extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name','slug','scientific_name','affected_crop','category','severity',
        'symptoms','cause','spread_mechanism','seasonal_info','treatment_steps',
        'prevention_methods','recommended_products','impact_stat',
        'spread_vector','peak_season','images','is_published',
    ];

    protected $casts = [
        'symptoms'             => 'array',
        'treatment_steps'      => 'array',
        'prevention_methods'   => 'array',
        'recommended_products' => 'array',
        'images'               => 'array',
        'is_published'         => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn (Disease $d) => $d->slug = $d->slug ?: Str::slug($d->name));
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(DiseaseAlert::class);
    }

    public function detections(): HasMany
    {
        return $this->hasMany(DiseaseDetection::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForCrop($query, string $crop)
    {
        return $query->where('affected_crop', $crop);
    }

    public function scopeSeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('affected_crop', 'like', "%{$term}%");
        });
    }

    public function recordView(): void
    {
        $this->increment('view_count');
    }
}
