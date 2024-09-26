<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToBentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bentos', function (Blueprint $table) {
            $table->decimal('original_price', 8, 2)->nullable()->after('price');
            $table->decimal('usual_discount_percentage', 5, 2)->nullable()->after('original_price');
            $table->decimal('usual_discounted_price', 8, 2)->nullable()->after('usual_discount_percentage');
            $table->time('estimated_discount_time')->nullable()->after('usual_discounted_price');
            $table->string('discount_status')->nullable()->after('estimated_discount_time');
            $table->string('stock_message')->nullable()->after('discount_status');
            $table->decimal('average_rating', 3, 2)->default(0)->after('stock_message');
            $table->integer('total_reviews')->default(0)->after('average_rating');
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
            $table->dropColumn([
                'original_price',
                'usual_discount_percentage',
                'usual_discounted_price',
                'estimated_discount_time',
                'discount_status',
                'stock_message',
                'average_rating',
                'total_reviews',
            ]);
        });
    }
}
