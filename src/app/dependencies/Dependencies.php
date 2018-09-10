<?php

$container = $app->getContainer();


$container['\routes\gets\CurrencyConverterController'] = function ($c) {
    $logger = $c->get('logger');
    $conversionService = $c->get('\services\CurrencyService');
    return new routes\gets\CurrencyConverterController($logger, $conversionService);
};

$container['\services\CurrencyService'] = function($c) {
    $logger = $c->get('logger');
    $xeConversion = $c->get('\services\conversionClients\XeConversion');
    $currencyDbManager = $c->get('\services\CurrencyDbManager');
    $redisService = $c->get('\services\cacheClients\RedisService');
    return new services\CurrencyService($logger, $xeConversion, $currencyDbManager, $redisService);    
};

$container['\services\conversionClients\XeConversion'] = function($c) {
    $pest = $c->get('pest');
    $logger = $c->get('logger');
    return new services\conversionClients\XeConversion($pest, $logger);
};

$container['\services\CurrencyDbManager'] = function($c) {
    $currencyDao = $c->get('\daos\CurrencyDao');
    return new services\CurrencyDbManager($currencyDao);
};

$container['\daos\CurrencyDao'] = function($c) {
    $logger = $c->get('logger');
    return new daos\CurrencyDao($logger);
};

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler(__DIR__."/../../../logs/app.log");
    $logger->pushHandler($file_handler);  
    return $logger;
};

$container['pest'] = function($c) {
    $logger = $c->get('logger');
    $pest = new services\Pest($logger);
    return $pest;
};

$container['\commands\CurrencyListJob'] = function($c) {
    $currencyService = $c->get('\services\CurrencyService');
    return new commands\CurrencyListJob($currencyService);
};

$container['\services\cacheClients\RedisService'] = function($c) {
    return new services\cacheClients\RedisService();
};