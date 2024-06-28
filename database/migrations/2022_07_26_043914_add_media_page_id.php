<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMediaPageId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_media', function (Blueprint $table) {
            $table->unsignedBigInteger('media_page_id')->after('media_id')->nullable();
             $table->foreign('media_page_id')->references('id')->on('media_pages')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_media', function (Blueprint $table) {
            //
        });
    }
}
