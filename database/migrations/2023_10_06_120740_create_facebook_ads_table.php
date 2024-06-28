<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_ads', function (Blueprint $table) {
            $table->id();
            $table->string('custom_id')->nullable()->unique();
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->string('request_type')->nullable();
            $table->string('request_filter')->nullable();
            $table->longText('response_json')->nullable();
            $table->longText('response_web')->nullable();
            $table->longText('response_api')->nullable();
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
        Schema::dropIfExists('facebook_ads');
    }
}
