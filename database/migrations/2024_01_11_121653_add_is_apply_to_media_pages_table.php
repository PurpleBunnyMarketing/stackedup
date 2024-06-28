<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsApplyToMediaPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_pages', function (Blueprint $table) {
            $table->date('apply_date')->nullable()->after('page_name')->comment('apply date');
            $table->boolean('is_apply')->default(0)->after('page_name')->comment('0: not apply, 1: apply');
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
            $table->dropColumn(['is_apply', 'apply_date']);
        });
    }
}
