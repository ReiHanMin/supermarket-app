<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chain extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'logo_url', 'contact_info'];

    public function stores()
    {
        return $this->hasMany(Store::class);
    }
}
