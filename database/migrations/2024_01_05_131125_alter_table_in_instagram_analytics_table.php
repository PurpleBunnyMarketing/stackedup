<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInInstagramAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instagram_analytics', function (Blueprint $table) {
            $table->date('month_year')->nullable()->after('response_web');
            $table->longText('month_data')->nullable()->after('response_web');
            $table->longText('day_data')->nullable()->after('response_web');
            $table->foreignId('media_page_id')->nullable()->after('user_id')->constrained('media_pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instagram_analytics', function (Blueprint $table) {
            $table->dropForeign(['media_page_id']);
            $table->dropColumn(['month_year', 'month_data', 'day_data', 'media_page_id']);
        });
    }
}
