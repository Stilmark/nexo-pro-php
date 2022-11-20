<?php

namespace Stilmark\Crypto;

class NexoPro
{

    static function getAccountSummary()
    {
        return self::request('accountSummary');
    }

    static function request($method, $params = [])
    {
        $client = new NexoPro();
        $ch = curl_init( 'https://'.$_ENV['NEXOPRO_API_URL'].'/api/'.$_ENV['NEXOPRO_API_VERSION'].'/'.$method );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $client->requestHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        // curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        // curl_setopt($ch, CURLOPT_HEADER, true);
        $result = curl_exec($ch);
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