<?php

// Permission for admin panel

use App\Models\SocialMediaDetail;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

if (!function_exists('getPermissions')) {
    function getPermissions($user_type = 'normal')
    {
        $permissions = array();

        if ($user_type == 'admin') {
            $permissions = [
                1 => [                                                   // Dashboard
                    'permissions' => 'access'
                ],
                2 => [                                                   // Users
                    'permissions' => 'access,view,edit,delete'
                ],
                3 => [ //Role Management
                    'permissions' => 'access,add,edit,delete'
                ],
                4 => [ //Country
                    'permissions' => 'access,add,edit,delete'
                ],
                5 => [ //State
                    'permissions' => 'access,add,edit,delete'
                ],
                6 => [ //City
                    'permissions' => 'access,add,edit,delete'
                ],
                7 => [ //CMS Pages
                    'permissions' => 'access,edit'
                ],
                8 => [ //Site Configurations
                    'permissions' => 'access'
                ],
                9 => [ //Manage FAQS
                    'permissions' => 'access,add,edit,delete'
                ],
                10 => [ //Manage Packages
                    'permissions' => 'access,view,add'
                ],
                11 => [ //Transaction Management
                    'permissions' => 'access'
                ],
                12 => [ //Manage Coupons
                    'permissions' => 'access,view,add,delete'
                ],
                13 => [ //Manage Subscribers
                    'permissions' => 'access'
                ],
                14 => [ //App Update Setting
                    'permissions' => 'access,edit'
                ],

            ];
        }

        return $permissions;
    }
}

// Call CURL
if (!function_exists('fireCURL')) {
    function fireCURL($url, $type, $headers, $data = NULL)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => strtoupper($type),
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        // dd($response);
        curl_close($curl);

        return json_decode($response, true);
    }
}

// Show number is cool format, like 1K, 2M, 50K etc
if (!function_exists('number_format_short')) {
    function number_format_short($n, $precision = 1)
    {
        if ($n < 900) {
            $n_format = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';
        } else if ($n < 900000000) {
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'M';
        } else if ($n < 900000000000) {
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'B';
        } else {
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $n_format = str_replace($dotzero, '', $n_format);
        }
        return $suffix != '' ? $n_format . ' ' . $suffix : $n_format;
    }
}

if (!function_exists('getUniqueString')) {
    function getUniqueString($table, $length = NULL)
    {
        $length = $length ?? config('utility.custom_length', 8);
        $field = 'custom_id';

        $string = \Illuminate\Support\Str::random($length);
        $found = \Illuminate\Support\Facades\DB::table($table)->where([$field => $string])->first();

        if ($found) {
            return getUniqueString($table, $field, $length);
        } else {
            return $string;
        }
    }
}

if (!function_exists('generateURL')) {
    function generateURL($file = "")
    {
        // dd($file);
        return App\Http\Controllers\Admin\HelperController::generateUrl($file);
    }
}

if (!function_exists('get_guard')) {
    function get_guard()
    {
        //You Need to define all guard created
        if (\Auth::guard('admin')->check()) {
            return "admin";
        } elseif (\Auth::guard('web')->check()) {
            return "user";
        } else {
            return "Guard not match";
        }
    }
}

if (!function_exists('getCountriesForAnalytic')) {
    function getCountriesForAnalytic($data = [])
    {

        $codes = array_keys($data);
        $resarr = [];
        $country = \App\Models\Country::whereIn('code', $codes)->pluck('name', 'code');


        foreach ($data as $key => $value) {
            if (isset($country[$key])) {
                $resarr[$country[$key]] = $value;
            }
        }
        return  $resarr;
    }
}

if (!function_exists('randomPassword')) {
    function randomPassword($len = 8)
    {

        //enforce min length 8
        if ($len < 8) {
            $len = 8;
        }

        //define character libraries - remove ambiguous characters like iIl|1 0oO
        $sets = array();
        $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        $sets[] = '123456789';
        $sets[] = '!*@#$%&';

        $password = '';

        //append a character from each set - gets first 4 characters
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
        }

        //use all characters to fill up to $len
        while (strlen($password) < $len) {
            //get a random set
            $randomSet = $sets[array_rand($sets)];

            //add a random char from the random set
            $password .= $randomSet[array_rand(str_split($randomSet))];
        }

        //shuffle the password string before returning!
        return str_shuffle($password);
    }
}

if (!function_exists('getMediaUrl')) {
    function getMediaUrl($media_key, $includeArr)
    {
        $key = array_search($media_key, array_column($includeArr, 'media_key'));
        $resArr = isset($includeArr[$key]) ? $includeArr[$key] : [];
        return (isset($resArr['type']) && $resArr['type'] == 'photo') ? $resArr['url'] : null;
    }
}

/** convert local timezone to utc  */
if (!function_exists('convertUTC')) {
    function convertUTC($dateTime, $timeZone = "Australia/Brisbane")
    {
        $newDateTime = new DateTime($dateTime, new DateTimeZone($timeZone));
        $newDateTime->setTimezone(new DateTimeZone("UTC"));
        return $newDateTime->format("Y-m-d H:i:s");
    }
}

/** convert any time to local  */
if (!function_exists('convertUTCtoLocal')) {
    function convertUTCtoLocal($dateTime, $timeZone = "Australia/Brisbane")
    {
        $dt = new DateTime(date($dateTime));
        $dt->setTimezone(new DateTimeZone($timeZone));
        return $dt->format('Y-m-d H:i:s');
    }
}

/** convert utc to local time but diffrent time for post type  */
if (!function_exists('convertUTCtoLocalDiffrentReturn')) {
    function convertUTCtoLocalDiffrentReturn($dateTime, $timeZone = "Australia/Brisbane")
    {
        $dt = new DateTime(date($dateTime));
        $dt->setTimezone(new DateTimeZone($timeZone));
        return $dt->format('M d, Y') . " at " . $dt->format('h:iA');
    }
}

/** convert utc to local in app side */
if (!function_exists('convertAppUTCtoLocal')) {
    function convertAppUTCtoLocal($dateTime, $timeZone = "Australia/Brisbane")
    {
        // dd($dateTime);
        $dt = new DateTime(date($dateTime));
        $dt->setTimezone(new DateTimeZone($timeZone));
        return $dt->format('M d Y, h:iA');
    }
}

if (!function_exists('updateToken')) {
    function updateToken($social_media_detail_id)
    {
        try {
            $socialMediaDetail = App\Models\SocialMediaDetail::where('id', $social_media_detail_id)->first();
            if (!empty($socialMediaDetail)) {

                if (\Carbon\Carbon::now() > $socialMediaDetail->token_expiry_time) {
                    $client = new Google\Client();
                    $client->setApplicationName('Google Analytics API');
                    $client->setScopes([Google\Service\Analytics::ANALYTICS_READONLY]);
                    $client->setClientId(config('utility.GOOGLE_CLIENT_ID'));
                    $client->setClientSecret(config('utility.GOOGLE_CLIENT_SECRET'));
                    $client->setRedirectUri(config('services.google.redirect'));
                    $client->setAccessToken($socialMediaDetail->token);

                    $access_token = $client->refreshToken($socialMediaDetail->refresh_token);
                    if (isset($access_token['access_token']) && !empty($access_token['access_token'])) {
                        $socialMediaDetail->update([
                            'token'             =>  $access_token['access_token'],
                            'token_expiry_time' =>  isset($access_token['expires_in']) ? \Carbon\Carbon::now()->addSeconds($access_token['expires_in']) : $socialMediaDetail->token_expiry_time,
                        ]);
                    }
                }

                return $socialMediaDetail->token;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }
}
if (!function_exists('checkMediaPageTokenExpiry')) {
    function checkMediaPageTokenExpiry($social_media_detail_id)
    {

        $social_media = SocialMediaDetail::where('id', $social_media_detail_id)->with('media')->first();
        if ($social_media->media->name == 'X(Twitter)') return false;

        if ($social_media->token_expiry < Carbon::parse(now())->toDateString()) return true;

        return false;
    }
}

if (!function_exists('getDatesBetweenTwoDates')) {
    function getDatesBetweenTwoDates($start_date, $end_date): array
    {
        $dates = [];
        $period = CarbonPeriod::create($start_date, $end_date);
        // Iterate over the period
        foreach ($period as $date) {
            $dates[] =  $date->format('Y-m-d');
        }
        return $dates;
    }
}
