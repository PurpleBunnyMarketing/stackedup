<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialMedialDetailIdColumnToMedialPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_pages', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('social_media_detail_id')->nullable();
            $table->foreign('social_media_detail_id')->references('id')->on('social_media_details')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media_pages', function (Blueprint $table) {
            //
            $table->dropColumn('social_media_detail_id');
        });
    }
}
