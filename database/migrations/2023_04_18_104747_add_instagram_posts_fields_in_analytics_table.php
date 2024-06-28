<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstagramPostsFieldsInAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analytics', function (Blueprint $table) {
            $table->longText('instagram_total_followers')->nullable();
            $table->longText('instagram_followers_gained')->nullable();
            $table->longText('instagram_reach')->nullable();
            $table->longText('instagram_impression')->nullable();
            $table->longText('instagram_profile_visits')->nullable();
            $table->longText('instagram_website_clicks')->nullable();
            $table->longText('instagram_clicks_data')->nullable();
            $table->longText('instagram_age_gender')->nullable();
            $table->longText('instagram_geo')->nullable();
            $table->longText('instagram_posts')->nullable();
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
            $table->dropColumn('instagram_total_followers');
            $table->dropColumn('instagram_followers_gained');
            $table->dropColumn('instagram_reach');
            $table->dropColumn('instagram_impression');
            $table->dropColumn('instagram_profile_visits');
            $table->dropColumn('instagram_website_clicks');
            $table->dropColumn('instagram_clicks_data');
            $table->dropColumn('instagram_age_gender');
            $table->dropColumn('instagram_geo');
            $table->dropColumn('instagram_posts');
        });
    }
}
