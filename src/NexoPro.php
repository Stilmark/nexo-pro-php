<?php

namespace Stilmark\Crypto;

class NexoPro
{

    ## Portfolio
    static function getAccountSummary()
    {
        return self::request('accountSummary');
    }

    static function getPairs()
    {
        return self::request('pairs');
    }

    static function getQuote(
        String $pair = 'BTC/USDT',
        String $side = 'buy',
        Float $amount = 1
    ){
        $param = [
        'GET' => compact(
            'pair',
            'amount',
            'side'
        )];
        return self::request('quote', $param);
    }

    ## Orders
    static function placeOrder(
        String $pair,
        String $side,
        String $type,
        Float $amount,
        Float $price = null
    ){
        $param = [
        'POST' => compact(
            'pair',
            'amount',
            'side',
            'type',
            'price'
        )];
        return self::request('orders', $param);
    }

    static function placeAdvancedOrder(
        String $pair,
        String $side,
        Float $amount,
        Float $stopLossPrice,
        Float $takeProfitPrice
    ){
        $param = [
        'POST' => compact(
            'pair',
            'side',
            'amount',
            'stopLossPrice',
            'takeProfitPrice'
        )];
        return self::request('orders/advanced', $param);
    }

    static function placeTriggerOrder(
        String $pair,
        Float $amount,
        String $side,
        Float $price,
        String $triggerType,
        Float $triggerPrice,
        Float $trailingDistance = null,
        Float $trailingPercentage = null
    ){
        $param = [
        'POST' => compact(
            'pair',
            'amount',
            'side',
            'price',
            'triggerType',
            'triggerPrice',
            'trailingDistance',
            'trailingPercentage',
        )];
        return self::request('orders/trigger', $param);
    }

    static function getOrder(
        String $id
    ){
        $param = [
        'GET' => compact(
            'id'
        )];
        return self::request('orderDetails', $param);
    }

    ## Transaction

    static function getTransaction(
        String $transactionId
    ){
        $param = [
        'GET' => compact(
            'transactionId'
        )];
        return self::request('trasaction', $param);
    }

    ## Cancel

    static function cancelOrder(String $orderId)
    {
        return self::request('orders/cancel', ['POST' => compact('orderId')]);
    }

    static function cancelAllOrders(String $pair)
    {
        return self::request('orders/cancel', ['POST' => compact('pair')]);
    }

    ## History

    static function getOrderHistory(
        Array $pairs = ['BTC/USDT'],
        String $startDate = 'yesterday',
        String $endDate = 'today',
        Int $pageSize = 50,
        Int $pageNumber =0
    ){
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $pageNum = 0;
        $param = [
        'GET' => compact(
            'pairs',
            'startDate',
            'endDate',
            'pageSize',
            'pageNum'
        )];
        return self::request('orders', $param);
    }

    static function getTradeHistory(
        Array $pairs = ['BTC/USDT'],
        String $startDate = 'yesterday',
        String $endDate = 'today',
        Int $pageSize = 50,
        Int $pageNumber = 0
    ){
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $pageNum = $pageNumber;
        $param = [
        'GET' => compact(
            'pairs',
            'startDate',
            'endDate',
            'pageSize',
            'pageNum'
        )];
        return self::request('trades', $param);
    }


    ## Request

    static function request($method, $params = [])
    {
        $client = new NexoPro();
        $apiUrl = 'https://'.$_ENV['NEXOPRO_API_URL'].'/api/'.$_ENV['NEXOPRO_API_VERSION'].'/'.$method;

        $requestParams = $client->validateParams(current($params));
        if (isset($requestParams['error'])) {
            return $requestParams;
        }

        if (count($params)) {
            if (key($params) == 'GET') {
                foreach($params['GET'] AS $key => $param) {
                    if (is_array($param)) {
                        $params['GET'][$key] = implode(',', $param);
                    }
                }
                $apiUrl .= '?'.http_build_query($params['GET']);
            }
        }

        $ch = curl_init( $apiUrl );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $client->requestHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        $json = curl_exec($ch);
        return json_decode($json, true);
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

    function validateParams($params)
    {
        $side = ['buy', 'sell'];
        $type = ['market', 'limit'];

        if (isset($params['side']) && !in_array($params['side'], $side)) {
            return ['error' => 'Invalid side option', 'Side options' => $side];
        }
        if (isset($params['type']) && !in_array($params['type'], $type)) {
            return ['error' => 'Invalid type option', 'Type options' => $type];
        }

        return true;
    }

}