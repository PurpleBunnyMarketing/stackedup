<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Media::truncate();
        Schema::enableForeignKeyConstraints();
        $media = [
            [
                'custom_id'     =>  getUniqueString('media'),
                'name'          =>  'Facebook',
                'image_url'     => asset('frontend/images/facebook.png'),
                'website_image_url' => asset('frontend/assets/media/images/facebook.svg'),
                'type' => 'social',
                'order_sequence' => 1,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     =>  getUniqueString('media'),
                'name'          =>  'Linkedin',
                'image_url'     => asset('frontend/images/linkedin.png'),
                'website_image_url'     => asset('frontend/assets/media/images/linkedin.svg'),
                'type' => 'social',
                'order_sequence' => 3,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     =>  getUniqueString('media'),
                'name'          =>  'X(Twitter)',
                'image_url'     => asset('frontend/images/twitter.png'),
                'website_image_url'     => asset('frontend/assets/media/images/twitter.svg'),
                'type' => 'social',
                'order_sequence' => 4,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     =>  getUniqueString('media'),
                'name'          =>  'Instagram',
                'image_url'     => asset('frontend/images/instagram.png'),
                'website_image_url'     => asset('frontend/assets/media/images/instagram.svg'),
                'type' => 'social',
                'order_sequence' => 2,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     =>  getUniqueString('media'),
                'name'          =>  'Google My Business',
                'image_url'     => asset('frontend/images/google-business.png'),
                'website_image_url'     => asset('frontend/assets/media/images/google-my-business.svg'),
                'type' => 'social',
                'order_sequence' => 5,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     =>  getUniqueString('media'),
                'name'          =>  'Google Analytics 4',
                'image_url'     => asset('frontend/images/google-analytics-icon.png'),
                'website_image_url'     => asset('frontend/assets/media/images/google-analytics-icon.svg'),
                'type'          => 'analytic',
                'order_sequence' => 6,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     =>  getUniqueString('media'),
                'name'          =>  'Google Ads',
                'image_url'     => asset('frontend/images/googleads-icon.png'),
                'website_image_url'     => asset('frontend/assets/media/images/google-ads-icon.svg'),
                'type'          => 'ads',
                'order_sequence' => 7,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     =>  getUniqueString('media'),
                'name'          =>  'Facebook Ads',
                'image_url'     => asset('frontend/images/facebook.png'),
                'website_image_url' => asset('frontend/assets/media/images/facebook.svg'),
                'type'          => 'ads',
                'order_sequence' => 8,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
        ];
        Media::insert($media);
    }
}
