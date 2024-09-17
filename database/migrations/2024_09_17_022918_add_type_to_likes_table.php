<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToLikesTable extends Migration
{
    public function up()
    {
        Schema::table('likes', function (Blueprint $table) {
            $table->string('type')->after('bento_id'); // Adding the 'type' column
        });
    }

    public function down()
    {
        Schema::table('likes', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
