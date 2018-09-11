<?php

require __DIR__.'/../../../vendor/autoload.php';

use Symfony\Component\Console\Application;
use commands\CurrencyListFetcherJob;

$container = new Slim\Container();
$app = new Slim\App($container);
require '../dependencies/Dependencies.php';
$command = new CurrencyListFetcherJob($container['\services\CurrencyService']);

$application = new Application();
$application->add($command);
$application->run();
