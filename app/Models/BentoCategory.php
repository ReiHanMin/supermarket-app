<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Bento;
use App\Models\Category;

class BentoCategory extends Model
{
    // The table associated with the model
    protected $table = 'bento_category'; // Assuming this is the name of your table

    // The attributes that are mass assignable
    protected $fillable = ['bento_id', 'category_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the bento associated with the category.
     */
    public function bento() // Corrected to singular, since it belongs to a single Bento
    {
        return $this->belongsTo(Bento::class);
    }

    /**
     * Get the category associated with the bento.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
