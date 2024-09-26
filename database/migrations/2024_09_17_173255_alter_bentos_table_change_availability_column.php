<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBentosTableChangeAvailabilityColumn extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bentos', function (Blueprint $table) {
            $table->string('availability')->change(); // Change the column type to string
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bentos', function (Blueprint $table) {
            $table->integer('availability')->change(); // Revert back to integer if needed
        });
    }
}
