<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Package::truncate();
        Schema::enableForeignKeyConstraints();
        $packages = [
            [
                'custom_id'     => getUniqueString('packages'),
                'product_id'    => config('utility.STRIPE_PRODUCT'),
                'price_id'      => 'price_1MABZ0EqKzNj8z3xImp6YTek',
                'package_type'  => 'monthly',
                'amount'        => '21',
                'description'   => '<ul><li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li><li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li><li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li></ul>',
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     =>  getUniqueString('packages'),
                'product_id'    => config('utility.STRIPE_PRODUCT'),
                'price_id'      => 'price_1MABZ0EqKzNj8z3xBC3JtnR0',
                'package_type'  => 'yearly',
                'amount'        => '192',
                'description'   => '<ul><li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li><li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li><li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li></ul>',
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],

        ];
        Package::insert($packages);
    }
}
