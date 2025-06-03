<?php

namespace App\Services;

use Dompdf\Exception;
use Illuminate\Support\Facades\Http;

class IziPayService
{



    public function mainKxU($DATA)
    {
        try {
            $uri = "https://api.izipay.ao/v1/referencias";
            $referenceRequest = $DATA;

            $jsonBody = json_encode($referenceRequest);
            echo "Request Body: " . $jsonBody . "\n";

            $hmacSignature = $this->generateHmacSignature($uri, 'POST', $jsonBody);
            echo "Generated HMAC Signature: " . $hmacSignature . "\n";

            echo "Sending request to: " . $uri . "\n";

            $ch = curl_init($uri);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Apikey ' . $hmacSignature,
                'Content-Type: application/json'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new Exception(curl_error($ch));
            }

            curl_close($ch);

            echo "Response Status Code: " . $httpCode . "\n";
            echo "Response Content: " . $response . "\n";

            return $httpCode;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
            return $httpCode;
        }
    }

    public function main()
    {
        try {
            $uri = "https://api.izipay.ao/v1/referencias";
            $referenceRequest = [
                'montante' => 8080,
                'validade' => date('Y-m-d\TH:i:s\Z', strtotime('+10 minutes'))
            ];

            $jsonBody = json_encode($referenceRequest);
            echo "Request Body: " . $jsonBody . "\n";

            $hmacSignature = $this->generateHmacSignature($uri, 'POST', $jsonBody);
            echo "Generated HMAC Signature: " . $hmacSignature . "\n";

            echo "Sending request to: " . $uri . "\n";

            $ch = curl_init($uri);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Apikey ' . $hmacSignature,
                'Content-Type: application/json'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new Exception(curl_error($ch));
            }

            curl_close($ch);

            echo "Response Status Code: " . $httpCode . "\n";
            echo "Response Content: " . $response . "\n";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
        }
    }

    private function generateHmacSignature($uri, $method, $body = null)
    {
        $appId = "16a853b6d01a4a2188bba340b0c40f2e";
        $appSecret = base64_decode("HWEVCUo//KbNuh7F41AMTa9+vruP4juhWmGRnqeNKco=");

        $content = '';
        $nonce = bin2hex(random_bytes(16));
        $timeStamp = time();

        if ($body !== null) {
            $bodyHash = md5($body, true);
            $content = base64_encode($bodyHash);
        }

        $message = "{$appId}{$method}{$uri}{$timeStamp}{$nonce}{$content}";
        $signature = hash_hmac('sha256', $message, $appSecret, true);
        $hashed = base64_encode($signature);

        return "{$appId}:{$hashed}:{$nonce}:{$timeStamp}";
    }

}
