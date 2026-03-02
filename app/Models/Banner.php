<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    /** @use HasFactory<\Database\Factories\BannerFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'image',
        'link',
        'position',
        'size',
        'is_active',
        'start_date',
        'end_date',
        'click_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'image_url',
        'position_name',
        'size_name'
    ];

    /**
     * The possible banner positions.
     */
    public const POSITIONS = [
        'header' => 'Header',
        'sidebar-top' => 'Sidebar Atas',
        'sidebar-bottom' => 'Sidebar Bawah',
        'footer' => 'Footer',
        'content-top' => 'Atas Konten',
        'content-bottom' => 'Bawah Konten',
        'opd-head-greeting' => 'Slide Ucapan Kepala OPD',
    ];

    /**
     * The possible banner sizes.
     */
    public const SIZES = [
        'small' => 'Small (300x250)',
        'medium' => 'Medium (728x90)',
        'large' => 'Large (970x250)',
        'responsive' => 'Responsive',
        'card-1x1' => 'Card 1x1 (Ucapan Kepala OPD)',
    ];

    /**
     * Scope a query to only include active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include banners within date range.
     */
    public function scopeWithinDateRange($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', now());
        })->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', now());
        });
    }

    /**
     * Check if banner is currently active based on date range.
     */
    public function isActive()
    {
        // If banner is marked as inactive, return false
        if (!$this->is_active) {
            return false;
        }

        // Check if within date range
        $now = now();
        $startDate = $this->start_date;
        $endDate = $this->end_date;

        // If no start date or start date is in the past
        $afterStartDate = is_null($startDate) || $startDate <= $now;
        // If no end date or end date is in the future
        $beforeEndDate = is_null($endDate) || $endDate >= $now;

        return $afterStartDate && $beforeEndDate;
    }

    /**
     * Get the image URL attribute.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        
        return null;
    }

    /**
     * Get the formatted position name.
     */
    public function getPositionNameAttribute()
    {
        return self::POSITIONS[$this->position] ?? ucfirst($this->position);
    }

    /**
     * Get the formatted size name.
     */
    public function getSizeNameAttribute()
    {
        return self::SIZES[$this->size] ?? ucfirst($this->size);
    }
}
