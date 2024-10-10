<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BentoStore extends Model
{
    use HasFactory;

    // Specify the table name since it's not the plural form of the model name
    protected $table = 'bento_store';

    // Allow mass assignment for the required fields
    protected $fillable = [
        'bento_id',
        'store_id',
        'current_discount',
        'stock_level',
    ];

    // Define relationships if needed (optional)
    public function bento()
    {
        return $this->belongsTo(Bento::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
