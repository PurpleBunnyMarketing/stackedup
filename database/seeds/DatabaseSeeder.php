<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SectionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(StatesSeeder::class);
        $this->call(CitiesSeeder::class);
        $this->call(CmsPageSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(MediaTableSeeder::class);
        $this->call(PackageTableSeeder::class);
        $this->call(AppSettingSeeder::class);
    }
}
