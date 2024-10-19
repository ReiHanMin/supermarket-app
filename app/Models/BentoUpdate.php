<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BentoUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'bento_id',
        'store_id',
        'discounted_price',
        'discount_percentage',
        'availability',
        'visit_time'
    ];

    public function bento()
    {
        return $this->belongsTo(Bento::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
