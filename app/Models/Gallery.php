<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
        'news_id'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function news()
    {
        return $this->belongsTo(News::class);
    }
    
    public function images()
    {
        return $this->hasMany(GalleryImage::class)->orderBy('sort_order');
    }
    
    /**
     * Get the categories for the gallery through its related news.
     */
    public function categories()
    {
        return $this->hasManyThrough(Category::class, News::class);
    }
}
