<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'gallery_id',
        'image_path',
        'caption',
        'sort_order'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }
}
