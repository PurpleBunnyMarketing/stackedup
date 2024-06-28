<?php

return [
    'custom_length'         =>  env('CUSTOM_ID_LENGTH', 8),
    'default_password'      =>  env('PASSWORD_LENGTH', 16),
    'caching'               =>  env('CACHE_ALLOW', false),
    'token'                 =>  env('TOKEN_NAME', 'stackedup'),
    'pagination'            =>  [
        'limit'     =>  env('DEFAULT_PAGINATION_LIMIT', 10),
        'offset'    =>  env('DEFAULT_PAGINATION_OFFSET', 0)
    ],
    'STRIPE_KEY'            => env('STRIPE_KEY', 'pk_live_51LzFwxGuRiPz8mQPhWoe3FsofmB5AmOTZIfaVDNJ43Oww9H3ELiR1sVAg34wdpERWYRQw6ElWWTyoqUHdXi4cJUt00tb9YaxKH'),
    'STRIPE_SECRET'         => env('STRIPE_SECRET', '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'),

    // TWITTER --------------------------------------------------------------
    'TWITTER_CLIENT_ID'  => env('TWITTER_CLIENT_ID'),
    'TWITTER_CLIENT_SECRET'  => env('TWITTER_CLIENT_SECRET'),

    'TWITTER_CONSUMER_KEY'  => env('TWITTER_CONSUMER_KEY'), // twitter api key
    'TWITTER_CONSUMER_SECRET'  => env('TWITTER_CONSUMER_SECRET'), // API Key Secret

    "GOOGLE_ENV"    => env('GOOGLE_ENV', 'testing'),

    // GOOGLE ---------------------------------------------------------------
    "GOOGLE_CLIENT_ID" => env('GOOGLE_CLIENT_ID'),
    "GOOGLE_CLIENT_SECRET" => env('GOOGLE_CLIENT_SECRET'),


    // GOOGLE ADS DEVELOPER TOKEN -------------------------------------------
    "GOOGLE_ADS_DEVELOPER_TOKEN" => env('GOOGLE_ADS_DEVELOPER_TOKEN'),
    // GOOGLE ADS REDIRECT URL
    "GOOGLE_ADS_REDIRECT_URL" => env('APP_URL') . '/auth/google_ads/callback',

    // FACEBOOK -------------------------------------------------------------
    "FACEBOOK_APP_ID" => env('FACEBOOK_APP_ID'),
    "FACEBOOK_APP_SECRET" => env('FACEBOOK_APP_SECRET'),

    // LINKEDIN -------------------------------------------------------------
    "LINKEDIN_CLIENT_ID" => env('LINKEDIN_CLIENT_ID'),
    "LINKEDIN_CLIENT_SECRET" => env('LINKEDIN_CLIENT_SECRET'),

    'PAGE_PRICE'            => env('DEFAULT_PAGE_PRICE', 21),

    'PAGE_PRICE_YEARLY'     => env('DEFAULT_PAGE_PRICE_YEARLY', 192),

    'STRIPE_PRODUCT'        => env('DEFAULT_PRODUCT_PRICE', 'prod_NtmJtKOFg8endf'),
    'STRIPE_TESTING_MONTHLY_PRICE_ID'        => env('STRIPE_TESTING_MONTHLY_PRICE_ID', 'prod_NtmJtKOFg8endf'),
    'STRIPE_TESTING_ANNUAL_PRICE_ID'        => env('STRIPE_TESTING_ANNUAL_PRICE_ID', 'prod_NtmJtKOFg8endf'),

    'google_analytics'  => [
        'request_expirations' =>    env('GOOGLE_ANALYTICS_REQUEST_EXPIRATION', 3600),
        'request_type'        =>    [
            'acquisition-all-line-chart'        =>  1,
            'acquisition-all-counts'            =>  2,
            'acquisition-all-rows'              =>  3,
            'acquisition-channels-line-charts'  =>  4,
            'acquisition-channels-pie-charts'   =>  5,
            'acquisition-channels-counts'       =>  6,
            'acquisition-paid-search-rows'      =>  7,
            'acquisition-organic-search-rows'   =>  8,
            'audience-location-country'         =>  9,
            'audience-location-region'          =>  10,
            'audience-location-city'            =>  11,
            'audience-languageCode'             =>  12,
            'audience-languagecode-line-charts' =>  13,
            'audience-userAgeBracket'           =>  14,
            'audience-userGender'               =>  15,
            'audience-mobileDeviceMarketingName'   =>  16,
            'audience-deviceCategory'           =>  17,
            'audience-deviceCategoryDate'       =>  18,
            'audience-browser'                  =>  19
        ]
    ],

    'social_media_file_size_limit' => [
        'facebook'              => '100000',    // In MB
        'instagram'             => '100',       // In MB
        'linkedin'              => '200',       // In MB
        'twitter'               => '512',       // In MB
    ],

    'DEFAULT_FACEBOOK_API_VERSION' => env('DEFAULT_FACEBOOK_API_VERSION', 'v18.0'),

    'facebook_ads' => [
        'request_expirations' =>    env('FACEBOOK_ADS_REQUEST_EXPIRATION', 7200),
    ],

    'instagram' => [
        'request_expirations' =>    env('INSTAGRAM_REQEST_EXPIRATION', 7200),
    ],
    'google_ads' => [
        'request_expirations' =>    env('GOOGLE_ADS_REQUEST_EXPIRATION', 3600),
    ],

];
