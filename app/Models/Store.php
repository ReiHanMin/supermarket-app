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
        'location',
        'contact_number',
        'status',
    ];

    /**
     * Define the relationship with Bento model.
     * A Store can have many Bentos.
     */
    public function bentos()
    {
        return $this->hasMany(Bento::class);
    }
}
