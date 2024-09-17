<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBentosTable extends Migration
{
    public function up()
    {
        Schema::table('bentos', function (Blueprint $table) {
            // Existing new fields
            $table->string('image_url')->nullable()->after('status');
            $table->text('ingredients')->nullable()->after('image_url');
            $table->integer('calories')->nullable()->after('ingredients');
            $table->integer('discount_percentage')->nullable()->after('calories');
            $table->boolean('availability')->default(true)->after('discount_percentage');
            $table->float('rating', 2, 1)->nullable()->after('availability');
            $table->integer('reviews_count')->default(0)->after('rating');

            // New store-related fields
            $table->string('store_name')->nullable()->after('reviews_count');
            $table->string('store_address')->nullable()->after('store_name');
            $table->string('store_email')->nullable()->after('store_address');
        });
    }

    public function down()
    {
        Schema::table('bentos', function (Blueprint $table) {
            // Drop all newly added fields
            $table->dropColumn([
                'image_url',
                'ingredients',
                'calories',
                'discount_percentage',
                'availability',
                'rating',
                'reviews_count',
                'store_name',
                'store_address',
                'store_email'
            ]);
        });
    }
}