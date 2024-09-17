<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['bento_id', 'user_id', 'comment'];

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
