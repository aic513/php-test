<?php

namespace Src;

use Analog\Analog;
use Closure;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Src\Services\UrlService;
use Throwable;

class Consumer
{
    private AMQPChannel $channel;
    private AMQPStreamConnection $connection;
    private Client $httpClient;
    private UrlService $service;

    private ProducerDelayed $producerDelayed;

    public function __construct(AMQPStreamConnection $connection, UrlService $service, ProducerDelayed $producerDelayed)
    {
        $this->connection = $connection;
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare('url-queue');
        $this->httpClient = new Client();
        $this->service = $service;
        $this->producerDelayed = $producerDelayed;
    }


    public function listen()
    {
        $callback = $this->getCallback();
        $this->channel->basic_consume(
            'url-queue',
            '',
            false,
            true,
            false,
            false,
            $callback
        );
        $this->listenQueue();
    }

    private function closeConnection()
    {
        $this->channel->close();
        $this->connection->close();
    }

    private function listenQueue()
    {
        while ($this->channel->is_open()) {
            $this->channel->wait();
        }
        $this->closeConnection();
    }

    private function getCallback(): Closure
    {
        return function ($msg) {
            echo ' [x] Consumer is listening message - ' . $msg->body . ' from rabbitmq' . PHP_EOL;
            $this->handleMessage($msg->body);
            sleep(30);
            echo ' [x] We sleep for 30 seconds' . PHP_EOL;
            echo ' [x] Done' . PHP_EOL;
        };
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    private function handleMessage(string $content)
    {
        $payload = json_decode($content, true);

        $preparedUrl = trim($payload['url'], '"');
        try {
            $response = $this->httpClient->get(
                $preparedUrl,
                [
                    'allow_redirects' => false,
                    'http_errors' => false,
                    'connect_timeout' => 10,
                    'verify' => false
                ]
            );
            $body = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();
            $headers = $response->getHeaders();
        } catch (ConnectException $exception) {
            $message = 'Connect Error: ' . $exception->getMessage();
            Analog::log($message);
        }

        $curlStatus = '';
        if (!isset($statusCode)) {
            $curlStatus .= 'cURL error';
            $statusCode = null;
            $headers = [];
            $this->service->updateUrl($preparedUrl, $body ?? null, $statusCode, $curlStatus, $headers);
        } else {
            echo $statusCode . PHP_EOL;
            if ($statusCode !== 200 && $payload['try'] === Producer::FIRST_TRY) {
                $this->producerDelayed->setPayloadUrl($payload['url']);
                $this->producerDelayed->send();
            } else {
                $curlStatus .= 'host available';
                $this->service->updateUrl($preparedUrl, $body ?? null, $statusCode, $curlStatus, $headers);
            }
        }
    }
}
