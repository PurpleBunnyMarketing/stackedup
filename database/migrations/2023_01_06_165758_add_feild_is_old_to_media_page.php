<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeildIsOldToMediaPage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_pages', function (Blueprint $table) {
            $table->enum('is_old', ['y', 'n'])->default('n')->nullable()->after('page_name');
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
            $table->dropColumn('is_old');
        });
    }
}
