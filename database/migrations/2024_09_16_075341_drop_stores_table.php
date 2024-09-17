<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropStoresTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('stores');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't need to recreate the table here as it will be created by the original migration
    }
}
