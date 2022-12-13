<?php

namespace Src;

use Enqueue\AmqpLib\AmqpConnectionFactory;
use Enqueue\AmqpLib\AmqpContext;
use Enqueue\AmqpTools\RabbitMqDlxDelayStrategy;
use Interop\Amqp\AmqpTopic as AmqpTopicAlias;
use Interop\Amqp\Impl\AmqpBind;
use Interop\Queue\Exception\DeliveryDelayNotSupportedException;
use Interop\Queue\Exception\Exception;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;

class ProducerDelayed
{
    private AmqpContext $context;
    private AmqpConnectionFactory $connectionFactory;
    private array $payload = [];
    const SECOND_TRY = 2;
    const DELAY_TIME = 15000;

    public function __construct(AmqpConnectionFactory $connectionFactory)
    {
        $this->connectionFactory = $connectionFactory;
        $this->context = $this->connectionFactory->createContext();
        $this->context->setDelayStrategy(new RabbitMqDlxDelayStrategy());
    }

    /**
     * @throws DeliveryDelayNotSupportedException
     * @throws Exception
     * @throws InvalidMessageException
     * @throws InvalidDestinationException
     * @throws \Interop\Queue\Exception
     */
    public function send()
    {
        $topic = $this->context->createTopic('url');
        $topic->setType(AmqpTopicAlias::TYPE_DIRECT);

        $queue = $this->context->createQueue('url-queue');

        $this->context->bind(new AmqpBind($topic, $queue));

        $this->setPayloadTry();

        $message = $this->context->createMessage(
            json_encode($this->getPayload(), JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE)
        );
        $this->context->createProducer()
            ->setDeliveryDelay(self::DELAY_TIME)
            ->send($queue, $message);
        echo '[x] [ProducerDelayed] Job created again - ' . $this->getPayload()['url'] . ' try - ' . $this->getPayload()['try'] . PHP_EOL;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param string $url
     */
    public function setPayloadUrl(string $url): void
    {
        $this->payload['url'] = $url;
    }

    private function setPayloadTry(): void
    {
        $this->payload['try'] = self::SECOND_TRY;
    }
}
