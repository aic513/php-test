<?php

use Enqueue\AmqpLib\AmqpConnectionFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Src\App;
use Src\Consumer;
use Src\Producer;
use Src\ProducerDelayed;
use Src\Router;
use Src\Services\UrlGenerator;
use Src\Services\UrlService;

return [
    Capsule::class => function () {
        $capsule = new Capsule();
        $capsule->addConnection(
            [
                'driver' => 'mysql',
                'host' => 'maria_db',
                'database' => 'dev',
                'username' => 'user',
                'password' => 'secret',
                'charset' => 'utf8',
                'prefix' => '',
                'strict' => false,
            ]
        );
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule;
    },
    AMQPStreamConnection::class => function () {
        return new AMQPStreamConnection(
            'rabbit-mq',
            $_ENV['RABBIT_PORT'],
            $_ENV['RABBIT_USER'],
            $_ENV['RABBIT_PASSWORD']
        );
    },
    AmqpConnectionFactory::class => function () {
        return new AmqpConnectionFactory('amqp://user:password@rabbit-mq:5672');
    },
    Producer::class => function (AMQPStreamConnection $connection, UrlGenerator $urlGenerator) {
        return new Producer($connection, $urlGenerator);
    },
    ProducerDelayed::class => function (AmqpConnectionFactory $connectionFactory) {
        return new ProducerDelayed($connectionFactory);
    },
    Consumer::class => function (
        AMQPStreamConnection $connection,
        UrlService $service,
        ProducerDelayed $producerDelayed
    ) {
        return new Consumer($connection, $service, $producerDelayed);
    },
    Router::class => function (Producer $producer, Consumer $consumer, UrlGenerator $urlGenerator) {
        return new Router($producer, $consumer, $urlGenerator);
    },
    App::class => function (Router $router) {
        return new App($router);
    },
    UrlService::class => function (Capsule $capsule) {
        return new UrlService($capsule);
    }
];
