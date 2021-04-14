<?php

require_once 'vendor/autoload.php';

use TelegramBot\Api\BotApi; //carregando a classe BotApi do TelegramBot

require_once './config.php';

for ($i=0; $i < count($url); $i++) { 
    try
    {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url[$i]);
        curl_setopt($curlHandle, CURLOPT_HEADER, true);
        curl_setopt($curlHandle, CURLOPT_NOBODY  , false);  // we don't need body
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curlHandle);
        $responseCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        $time = curl_getinfo($curlHandle, CURLINFO_CONNECT_TIME);
        curl_close($curlHandle);
    } catch (\Throwable $e) { //capturando o response code caso o sistema retorne 404
        $responseCode = '404';
    }

    if ($responseCode == '200'){
        if($time > 0.240){
            $bot = new \TelegramBot\Api\BotApi($botId);
            $bot->sendMessage($chatId, 'O site ' . $url[$i] . ' demorou mais de 240ms para carregar');
        }
    }else if($responseCode == '404'){
        $bot = new \TelegramBot\Api\BotApi($botId);
        $bot->sendMessage($chatId, 'O site ' . $url[$i] . ' está offline'); 
    }else if($responseCode == '503' || $responseCode == '500'){
        $bot = new \TelegramBot\Api\BotApi($botId);
        $bot->sendMessage($chatId, 'O site ' . $url[$i] . ' está com erro interno'); 
    }
    else{
        $bot = new \TelegramBot\Api\BotApi($botId);
        $bot->sendMessage($chatId, 'O site ' . $url[$i] . ' está retornando o codigo '. $responseCode); 
    }
}