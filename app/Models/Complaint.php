<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Events\ComplaintSubmitted;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'category',
        'description',
        'status',
        'document',
        'photo',
        'response'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    protected static function booted()
    {
        static::created(function ($complaint) {
            // Dispatch event ketika aduan baru dibuat
            ComplaintSubmitted::dispatch($complaint);
        });
    }
    
    /**
     * Get complaint counts by status
     *
     * @return array
     */
    public static function getCountsByStatus()
    {
        return static::select('status')
            ->selectRaw('count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
    
    /**
     * Get total complaint count
     *
     * @return int
     */
    public static function getTotalCount()
    {
        return static::count();
    }
}
