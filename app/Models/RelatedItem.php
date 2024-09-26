<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatedItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bento_id',
        'related_item_name',
        'original_price',
        'discounted_price',
        'similarity_score',
    ];

    /**
     * Get the bento that owns the related item.
     */
    public function bento()
    {
        return $this->belongsTo(Bento::class);
    }
}
