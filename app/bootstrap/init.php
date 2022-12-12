<?php

declare(strict_types=1);

use Analog\Analog;
use Analog\Handler\File;
use DI\ContainerBuilder;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

Analog::handler(File::init('./logs-app/analog.log'));

/**
 * @noinspection PhpUnhandledExceptionInspection
*/
$container = (new ContainerBuilder())
    ->addDefinitions(dirname(__FILE__) . '/definitions.php')
    ->build();

return $container;
