<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrUpdateRelatedItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_items', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('bento_id'); // Foreign key to bentos table
            $table->string('related_item_name'); // Name of the related item
            $table->decimal('original_price', 8, 2)->nullable(); // Original price of the related item
            $table->decimal('discounted_price', 8, 2)->nullable(); // Discounted price of the related item
            $table->decimal('similarity_score', 5, 2)->nullable(); // Optional similarity score
            $table->timestamps(); // Timestamps for created_at and updated_at

            // Foreign key constraint to link to bentos table
            $table->foreign('bento_id')->references('id')->on('bentos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_items');
    }
}
