<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bento extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'calories',
        'availability',
        'discount_percentage',
        'rating',
        'reviews_count',
        'store_id',
        'image_url',
        'created_by',
        'updated_by'
    ];

    // Define relationship to comments
    public function likes()
{
    return $this->hasMany(Like::class)->where('type', 'like'); // Filter only 'like' type
}

public function dislikes()
{
    return $this->hasMany(Like::class)->where('type', 'dislike'); // Filter only 'dislike' type
}

public function comments()
{
    return $this->hasMany(Comment::class); // Relationship to comments
}


    // Helper method to calculate total likes
    public function totalLikes()
    {
        return $this->likes()->count();
    }

    // Helper method to calculate total dislikes
    public function totalDislikes()
    {
        return $this->dislikes()->count();
    }
}
