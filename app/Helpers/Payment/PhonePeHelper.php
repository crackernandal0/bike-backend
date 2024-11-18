<?php


namespace App\Helpers\Payment;

use GuzzleHttp\Client;
use Ixudra\Curl\Facades\Curl;

class PhonePeHelper
{
    public static function makePayment($merchantTransactionId, $merchantUserId, $amount, $callbackUrl, $mobileNumber = null)
    {
        $apiKey = env('PHONEPE_API_KEY');
        $merchantId = env('PHONEPE_MERCHANT_ID');
        $keyIndex = env('PHONEPE_KEY_INDEX');
        $mode = env('PHONEPE_MODE', 'dev');

        $baseUrl = $mode === 'prod'
            ? 'https://api.phonepe.com/apis/hermes/pg/v1/pay'
            : 'https://api-preprod.phonepe.com/apis/hermes/pg/v1/pay';

        $paymentData = [
            'merchantId' => $merchantId,
            'merchantTransactionId' => $merchantTransactionId,
            'merchantUserId' => $merchantUserId,
            'amount' => $amount, // Amount in paisa
            'redirectUrl' => $callbackUrl,
            'redirectMode' => 'REDIRECT',
            'callbackUrl' => $callbackUrl,
            'mobileNumber' => $mobileNumber,
            'paymentInstrument' => [
                'type' => 'PAY_PAGE',
            ]
        ];

        $payload = base64_encode(json_encode($paymentData));
        $finalPayload = $payload . "/pg/v1/pay" . $apiKey;
        $sha256 = hash("sha256", $finalPayload);
        $xVerify = $sha256 . '###' . $keyIndex;

        $client = new Client();
        try {
            $response = $client->post($baseUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-VERIFY' => $xVerify,
                    'accept' => 'application/json',
                ],
                'json' => ['request' => $payload],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if (isset($responseBody['success']) && $responseBody['success'] == '1') {
                $payUrl = $responseBody['data']['instrumentResponse']['redirectInfo']['url'];
                // return redirect()->away($payUrl);
                return [
                    'status' => true,
                    'gateway_url' => $payUrl,
                ];
            } else {
                return [
                    'status' => false,
                    'message' => $responseBody['message'] ?? 'Payment failed',
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'An error occurred while processing the payment: ' . $e->getMessage(),
            ];
        }
    }

    public static function checkStatus($merchantTransactionId) {
        $merchantId = env('PHONEPE_MERCHANT_ID');
        $saltKey = env('PHONEPE_SALT_KEY');
        $saltIndex = env('PHONEPE_SALT_INDEX');
        $mode = env('PHONEPE_MODE', 'PRODUCTION');

        $finalXHeader = hash('sha256','/pg/v1/status/'.$merchantId.'/'.$merchantTransactionId.$saltKey).'###'.$saltIndex;

        $response = Curl::to('https://api-preprod.phonepe.com/apis/merchant-simulator/pg/v1/status/'.$merchantId.'/'.$merchantTransactionId)
                ->withHeader('Content-Type:application/json')
                ->withHeader('accept:application/json')
                ->withHeader('X-VERIFY:'.$finalXHeader)
                ->withHeader('X-MERCHANT-ID:'.$merchantTransactionId)
                ->get();

                return $response;
        $baseUrl = 'https://api.phonepe.com/apis/hermes/pg/v1/status/';

            $finalXHeader = hash('sha256', $baseUrl . $merchantId . '/' . $merchantTransactionId . $saltKey) . '###' . $saltIndex;
        
            $client = new Client();
        
            $response = $client->request(
                'GET',
                $baseUrl . $merchantId . '/' . $merchantTransactionId,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'X-VERIFY' => $finalXHeader,
                        'X-MERCHANT-ID' => $merchantId,
                    ],
                ]
            );
        
            return json_decode($response->getBody(), true);
        $baseUrl = $mode === 'PRODUCTION'
            ? 'https://api.phonepe.com/apis/hermes/pg/v1/status/'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/status/';

        // Construct the URL with merchantId and merchantTransactionId
        $url = $baseUrl . $merchantId . '/' . $merchantTransactionId;

        // Create the string to be hashed for X-VERIFY
        $verifyString = "/pg/v1/status/" . $merchantId . "/" . $merchantTransactionId . $saltKey;
        $sha256 = hash("sha256", $verifyString);
        $xVerify = $sha256 . '###' . $saltIndex;

        $client = new Client();
return $xVerify;
        try {
            // Send GET request to check the status
            $response = $client->get($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-VERIFY' => $xVerify,
                    'X-MERCHANT-ID' => $merchantId,
                    'accept' => 'application/json',
                ]
            ]);

            $responseBody = json_decode($response->getBody(), true);

            return $responseBody;
            if (isset($responseBody['code']) && $responseBody['code'] == 'PAYMENT_SUCCESS') {
                return [
                    'status' => true,
                    'message' => 'Payment successful',
                    'data' => $responseBody['data'],
                ];
            } elseif ($responseBody['code'] == 'PAYMENT_PENDING') {
                return [
                    'status' => false,
                    'message' => 'Payment is pending. Please check later.',
                    'data' => $responseBody['data'],
                ];
            } else {
                return [
                    'status' => false,
                    'message' => $responseBody['message'] ?? 'Payment failed or unknown status',
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'An error occurred while checking the payment status: ' . $e->getMessage(),
            ];
        }
    }
}
