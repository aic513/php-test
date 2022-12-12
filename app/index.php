<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Src\App;

try {
    /** @var ContainerInterface $container */
    $container = include __DIR__ . '/bootstrap/init.php';
    $app = $container->get(App::class);
    $capsule = $container->get(Capsule::class);
    $app->run($argv);
} catch (Exception|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
    echo $e->getMessage() . PHP_EOL;
}