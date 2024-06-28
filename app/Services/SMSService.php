<?php

namespace App\Services;

class SMSService
{
    public function send($mobileNo, $otp): string
    {
        $user = "matt@purplebunny.com.au";
        $pass = "Purple12345678";
        $to = $mobileNo;
        $from = "Stackedup";

        $message = urlencode("Dear Client,The One Time Password(OTP) for your account login is " . $otp . ". Please do not share your OTP with anyone. Team stackedup!");

        $url = "http://api.smsbroadcast.com.au/api.php?" .
            "username=$user&password=$pass&from=$from&to=$to&message=$message";
        return file_get_contents($url);
    }
}
