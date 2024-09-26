<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bentos', function (Blueprint $table) {
            // Drop columns that exist in the table
            if (Schema::hasColumn('bentos', 'average_rating')) {
                $table->dropColumn('average_rating');
            }
            if (Schema::hasColumn('bentos', 'total_reviews')) {
                $table->dropColumn('total_reviews');
            }
            if (Schema::hasColumn('bentos', 'rating')) {
                $table->dropColumn('rating');
            }
            if (Schema::hasColumn('bentos', 'reviews_count')) {
                $table->dropColumn('reviews_count');
            }
            if (Schema::hasColumn('bentos', 'ingredients')) {
                $table->dropColumn('ingredients');
            }
            if (Schema::hasColumn('bentos', 'discount_status')) {
                $table->dropColumn('discount_status');
            }

            // Modify existing fields (removed 'price')
            $table->decimal('original_price', 8, 2)->nullable()->change();
            $table->decimal('usual_discount_percentage', 5, 2)->nullable()->change();
            $table->decimal('usual_discounted_price', 8, 2)->nullable()->change();
            $table->decimal('discount_percentage', 5, 2)->nullable()->change();
            $table->time('estimated_discount_time')->nullable()->change();
            $table->integer('calories')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bentos', function (Blueprint $table) {
            // Restore dropped columns
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->float('rating')->nullable();
            $table->integer('reviews_count')->default(0);
            $table->text('ingredients')->nullable();
            $table->string('discount_status')->nullable();

            // Revert column modifications
            $table->decimal('original_price', 8, 2)->nullable(false)->change();
            $table->decimal('usual_discount_percentage', 5, 2)->nullable(false)->change();
            $table->decimal('usual_discounted_price', 8, 2)->nullable(false)->change();
            $table->decimal('discount_percentage', 5, 2)->nullable(false)->change();
            $table->time('estimated_discount_time')->nullable(false)->change();
            $table->integer('calories')->nullable(false)->change();
        });
    }
}
