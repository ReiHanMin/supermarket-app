<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateAvailabilityValuesInBentoUpdatesTable extends Migration
{
    public function up()
    {
        // Set all availability values to 0 to avoid issues with column conversion
        DB::table('bento_updates')->update(['availability' => 0]);
    }

    public function down()
    {
        // Optionally restore the values, if needed
    }
}


