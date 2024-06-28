<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogleFieldsInAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analytics', function (Blueprint $table) {
            $table->datetime('google_last_updated')->nullable();
            $table->text('google_plateform_device')->nullable();
            $table->text('google_rating_and_review_count')->nullable();
            $table->text('google_calls_website_direction')->nullable();
            $table->text('google_messages_bookings_food_order')->nullable();
            $table->text('google_reviews')->nullable();
            $table->text('google_posts')->nullable();
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
            $table->dropColumn(['google_last_updated', 'google_plateform_device', 'google_rating_and_review_count', 'google_calls_website_direction', 'google_messages_bookings_food_order', 'google_reviews', 'google_posts']);
        });
    }
}
