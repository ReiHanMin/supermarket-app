<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'chain_name',
        'address',
        'email',
        'phone',
        'photo'
    ];

    /**
     * Define the relationship with Bento model.
     * A Store can have many Bentos.
     */
    public function bentos()
    {
    return $this->belongsToMany(Bento::class, 'bento_store')
                ->withPivot('current_discount', 'stock_level')
                ->withTimestamps();
    }


    public function chain()
    {
    return $this->belongsTo(Chain::class);
    }

}
