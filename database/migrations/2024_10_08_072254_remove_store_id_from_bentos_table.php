<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveStoreIdFromBentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bentos', function (Blueprint $table) {
            // Drop the foreign key constraint before dropping the column
            $table->dropForeign(['store_id']); // Replace 'store_id' with the actual foreign key constraint name if needed
            $table->dropColumn('store_id');
        });
    }
    
    public function down()
    {
        Schema::table('bentos', function (Blueprint $table) {
            // Restore the column and foreign key constraint in the down method if rolling back
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });
    }
}    