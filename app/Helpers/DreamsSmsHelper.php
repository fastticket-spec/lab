<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class DreamsSmsHelper
{
    public static function sendSMS(string $to, string $message): void
    {
        $sender = config('dreams_sms.sender');
        $to = strpos($to, '+') > -1 ? explode('+', $to)[1] : $to;
        \Log::debug($to);

        $user = config('dreams_sms.user');
        $password = config('dreams_sms.password');
        $date = now()->format('Y-m-d');
        $time = now()->format('H:i:s');

        $response = Http::get("https://www.dreams.sa/index.php/api/sendsms/?user=$user&pass=$password&to=$to&message=$message&sender=$sender&date=$date&time=$time");

        if ($response->failed() || $response->body() !== 'Success') {
            \Log::debug('dreams sms: ' . $response->body());
        }
    }
}
