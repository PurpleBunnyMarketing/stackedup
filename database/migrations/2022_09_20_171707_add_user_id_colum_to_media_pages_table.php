<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdColumToMediaPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_pages', function (Blueprint $table) {
            $table->foreignId('user_id')->after('media_id')->nullable()->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
            $table->dropForeign('media_pages_user_id_foreign');
            $table->dropIndex('media_pages_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
