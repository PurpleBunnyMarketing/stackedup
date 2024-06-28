<?php

namespace Database\Seeders;

use App\Models\AppUpdateSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $appSettings = [
            [
                'slug' => 'android',
                'build_version' => '1',
                'app_version' => '1.0.0',
                'is_force_update' => 0,
            ],
            [
                'slug' => 'ios',
                'build_version' => '1',
                'app_version' => '1.0.0',
                'is_force_update' => 0,
            ]
        ];

        $appSettingsChunks = array_chunk($appSettings, 2000);
        foreach ($appSettingsChunks as $setting) {
            AppUpdateSetting::insert($setting);
        }
    }
}
