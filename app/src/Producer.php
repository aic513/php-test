<?php

namespace Src;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Src\Services\UrlGenerator;

class Producer
{
    private $channel;

    private AMQPStreamConnection $connection;

    private UrlGenerator $urlGenerator;

    const FIRST_TRY = 1;

    public function __construct(AMQPStreamConnection $connection, UrlGenerator $urlGenerator)
    {
        $this->connection = $connection;
        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare('url', 'direct');
        $this->channel->queue_declare('url-queue');
        $this->channel->queue_bind('url-queue', 'url', 'url-queue');
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws Exception
     */
    public function push()
    {
        foreach ($this->urlGenerator->getUrls() as $url) {
            $msg = new AMQPMessage(
                json_encode(
                    [
                        'url' => $url,
                        'try' => self::FIRST_TRY
                    ],
                    JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE
                ),
                ['delivery_mode' => 2]
            );
            $this->channel->basic_publish($msg, 'url', 'url-queue');
            echo '[x] Producer Job created - ' . $url . PHP_EOL;
            sleep(1);
        }
        $this->closeConnection();
    }

    /**
     * @throws Exception
     */
    public function closeConnection()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
