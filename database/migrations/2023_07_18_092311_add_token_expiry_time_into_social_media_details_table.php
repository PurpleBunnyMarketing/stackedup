<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokenExpiryTimeIntoSocialMediaDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_media_details', function (Blueprint $table) {
            $table->dateTime('token_expiry_time')->after('token_expiry')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_media_details', function (Blueprint $table) {
            $table->dropColumn('token_expiry_time');
        });
    }
}
