<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveStockMessageFromBentoUpdatesTable extends Migration
{
    public function up()
    {
        Schema::table('bento_updates', function (Blueprint $table) {
            $table->dropColumn('stock_message');
        });
    }

    public function down()
    {
        Schema::table('bento_updates', function (Blueprint $table) {
            $table->string('stock_message')->nullable();
        });
    }
}
