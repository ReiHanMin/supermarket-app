<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bento_id')->constrained()->onDelete('cascade'); // References the Bento ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // References the User ID
            $table->text('comment'); // Stores the comment text
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
}