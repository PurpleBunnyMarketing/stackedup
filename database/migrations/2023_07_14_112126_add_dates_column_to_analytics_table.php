<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatesColumnToAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analytics', function (Blueprint $table) {
            //
            $table->datetime('facebook_reach_last_updated_at')->after('facebook_like_analytic')->nullable();
            $table->datetime('facebook_engagement_last_updated_at')->after('facebook_like_analytic')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('analytics', function (Blueprint $table) {
            //
            $table->dropColumn('facebook_reach_last_updated_at');
            $table->dropColumn('facebook_engagement_last_updated_at');
        });
    }
}
