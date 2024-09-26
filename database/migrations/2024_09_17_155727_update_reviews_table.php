<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Ensure the bento_id field exists, if not already added
            if (!Schema::hasColumn('reviews', 'bento_id')) {
                $table->unsignedBigInteger('bento_id')->after('id');
                $table->foreign('bento_id')->references('id')->on('bentos')->onDelete('cascade');
            }

            // Ensure the rating field exists, if not already added
            if (!Schema::hasColumn('reviews', 'rating')) {
                $table->decimal('rating', 3, 2)->after('bento_id')->default(0);
            }

            // Ensure the review_text field exists, if not already added
            if (!Schema::hasColumn('reviews', 'review_text')) {
                $table->text('review_text')->nullable()->after('rating');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['bento_id']);
            $table->dropColumn(['bento_id', 'rating', 'review_text']);
        });
    }
}
