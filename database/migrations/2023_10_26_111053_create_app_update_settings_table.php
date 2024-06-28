<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppUpdateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_update_settings', function (Blueprint $table) {
            $table->id();
            // $table->uuid("id")->primary();
            $table->string('slug');
            $table->string('build_version');
            $table->string('app_version');
            $table->tinyInteger('is_force_update')->nullable();
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
        Schema::dropIfExists('app_update_settings');
    }
}
