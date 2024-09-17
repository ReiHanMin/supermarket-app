<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBentoImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bento_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bento_id')->constrained()->onDelete('cascade'); // Foreign key to Bentos table
            $table->string('path'); // Path to the image
            $table->string('url')->nullable(); // Full URL for the image
            $table->string('mime')->nullable(); // MIME type of the image
            $table->integer('size')->nullable(); // Size of the image file
            $table->integer('position')->default(0); // Position of the image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bento_images');
    }
}
