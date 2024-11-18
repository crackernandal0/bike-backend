<?php

namespace App\Helpers\Auth;


class AuthHelper
{
    public static function sendSMS($to, $var1, $var2)
    {
        $msg = 'Something went wrong.';
        $info = null;

        $apiLink = env('SMS_API_LINK');
        $apikey = env('SMS_API_KEY');

        $dataString = '&templatename=OTP@&var1=' . $var1 . '&var2=' . $var2 . '';


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiLink,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'module=TRANS_SMS&apikey=' . $apikey . '&to=' . $to . '&from=FEMIRI' . $dataString,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $resp = json_decode($response, true);

        if ($resp['Status']) {
            $info = $resp['Details'];
        } else {
            $msg = 'SMS sending failed. Please try again later.';
        }

        return json_encode(['response' => $resp]);
    }
}
