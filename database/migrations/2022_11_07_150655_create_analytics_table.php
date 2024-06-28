<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->string('custom_id')->nullable();
            $table->longtext('facebook_like_analytic')->nullable();
            $table->longtext('facebook_engaged_analytic')->nullable();
            $table->longtext('facebook_react_analytic')->nullable();
            $table->longtext('twitter_analytic')->nullable();
            $table->longtext('twitter_follower_analytic')->nullable();
            $table->longtext('twitter_tweet_analytic')->nullable();
            $table->longtext('linkedin_follower')->nullable();
            $table->longtext('linkedin_time_follower')->nullable();
            $table->longtext('linkedin_time_click')->nullable();
            $table->longtext('linkedin_geo_data')->nullable();
            $table->longtext('linkedin_seniority_data')->nullable();
            $table->longtext('linkedin_function_data')->nullable();
            $table->longtext('linkedin_industry_data')->nullable();
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('analytics');
    }
}
