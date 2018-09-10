<?php
require '../../vendor/autoload.php';
$container = new \Slim\Container;
$app = new \Slim\App($container);
require 'dependencies/Dependencies.php';
require 'routes.php';
