<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWeeklyVisitsUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement('DROP VIEW IF EXISTS userViews');


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
                         CREATE VIEW userViews AS
                                              (
                                                SELECT
                                                    COUNT(user_id) as counter,
                                                    user_id FROM
                                                     views
                                                     WHERE created_at >= DATE(NOW()) - INTERVAL 7 DAY
                                                     GROUP BY user_id
                                                     ORDER BY counter DESC
                                              )
                              ");
    }
}
