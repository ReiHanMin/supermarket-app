<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStoresTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            // Add new columns
            if (!Schema::hasColumn('stores', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('stores', 'address')) {
                $table->string('address')->nullable()->after('name');
            }
            if (!Schema::hasColumn('stores', 'email')) {
                $table->string('email', 320)->nullable();
            } else {
                $table->string('email', 320)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('stores', function (Blueprint $table) {
        // Remove the columns that were added in the 'up' method
        $table->dropColumn('address');

        // Revert changes to existing columns
        $table->string('email')->nullable(false)->change();

        // Note: We don't drop 'name' as it already existed
        // We also don't touch 'location' as it wasn't modified in the 'up' method
    });
}
}