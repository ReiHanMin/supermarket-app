<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['bento_id', 'user_id', 'like', 'type'];

    

    // Define inverse relationship to Bento
    public function bento()
    {
        return $this->belongsTo(Bento::class);
    }

    // Define inverse relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
