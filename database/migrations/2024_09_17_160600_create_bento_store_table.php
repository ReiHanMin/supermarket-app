<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBentoStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bento_store', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('bento_id'); // Foreign key to bentos table
            $table->unsignedBigInteger('store_id'); // Foreign key to stores table
            $table->decimal('current_discount', 5, 2)->nullable(); // Current discount percentage
            $table->integer('stock_level')->nullable(); // Stock level (optional)
            $table->timestamps(); // Timestamps for created_at and updated_at

            // Foreign key constraints
            $table->foreign('bento_id')->references('id')->on('bentos')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bento_store');
    }
}
