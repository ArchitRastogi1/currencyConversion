<?php

require __DIR__.'/../../../vendor/autoload.php';

use commands\CurrencyListJob;
use Symfony\Component\Console\Application;
use commands\CurrencyRatesFetcherJob;

$container = new Slim\Container();
$app = new Slim\App($container);
require '../dependencies/Dependencies.php';
$command = new CurrencyRatesFetcherJob($container['\services\CurrencyService']);

$application = new Application();
$application->add($command);
$application->run();
