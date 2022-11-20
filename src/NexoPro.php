<?php

namespace Stilmark\Crypto;

class NexoPro
{

    static function getAccountSummary()
    {
        return self::request('accountSummary');
    }

    static function getPairs()
    {
        return self::request('pairs');
    }

    static function getQuote(
        String $pair,
        String $side,
        Float $amount
    ){
        return self::request('quote', ['GET' => compact('pair', 'amount', 'side')]);
    }

    ## Orders

    static function placeOrder(String $pair, Float $amount, String $side, String $type, Float $price = null)
    {
        return self::request('orders', ['POST' => compact('pair', 'amount', 'side', 'type', 'price')]);
    }

    static function placeTriggerOrder(String $pair, Float $amount, String $side, Float $price,
                String $triggerType, Float $triggerPrice, Float $trailingDistance, Float $trailingPercentage)
    {
        return self::request('orders/trigger', ['POST' => compact('pair', 'amount', 'side', 'type', 'price')]);
    }

    static function cancelOrder(String $orderId)
    {
        return self::request('orders/cancel', ['POST' => compact('orderId')]);
    }

    static function cancelAllOrders(String $pair)
    {
        return self::request('orders/cancel', ['POST' => compact('pair')]);
    }


    static function request($method, $params = [])
    {
        $client = new NexoPro();
        $apiUrl = 'https://'.$_ENV['NEXOPRO_API_URL'].'/api/'.$_ENV['NEXOPRO_API_VERSION'].'/'.$method;

        if (count($params)) {
            if (key($params) == 'GET') {
                $apiUrl .= '?'.http_build_query($params['GET']);
            }
        }

        $ch = curl_init( $apiUrl );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $client->requestHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        // curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        // curl_setopt($ch, CURLOPT_HEADER, true);
        $result = curl_exec($ch);
        if (!$result) {
            return curl_getinfo($ch);
        }
        return $result;
    }

    function requestHeaders()
    {
        $nonce = strval(time()*1000);
        $signature = hash_hmac('sha256', $nonce, $_ENV['NEXOPRO_API_SECRET'], true);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: nexo-pro-client',
            'X-API-KEY: '.$_ENV['NEXOPRO_API_KEY'],
            'X-NONCE: '.$nonce,
            'X-SIGNATURE: '.base64_encode($signature),
        ];

        return $headers;
    }

}