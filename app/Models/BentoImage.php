<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BentoImage extends Model
{
    use HasFactory;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'bento_id',
        'path',
        'url',
        'mime',
        'size',
        'position'
    ];

    /**
     * Get the full URL for the image.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->url ?? url('storage/' . $this->path);
    }

    /**
     * Define the relationship with the Bento model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bento()
    {
        return $this->belongsTo(Bento::class);
    }
}
