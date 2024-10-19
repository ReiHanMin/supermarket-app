<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBentoUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bento_updates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bento_id');
            $table->decimal('discounted_price', 8, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->string('availability', 255)->nullable();
            $table->timestamp('visit_time')->nullable();
            $table->timestamps();

            // Foreign key constraint linking to the bentos table
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
        Schema::dropIfExists('bento_updates');
    }
}
