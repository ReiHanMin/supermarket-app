<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('bento_store', function (Blueprint $table) {
        $table->string('stock_level')->change();  // Change stock_level to string
    });
}

public function down()
{
    Schema::table('bento_store', function (Blueprint $table) {
        $table->integer('stock_level')->change();  // Revert back to integer if rolled back
    });
}

};
