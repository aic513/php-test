<?php

namespace Src;

use Exception;
use Src\Services\UrlGenerator;

class Router
{
    private array|null $argv;
    private Producer $producer;
    private Consumer $consumer;
    private UrlGenerator $urlGenerator;

    public function __construct(Producer $producer, Consumer $consumer, UrlGenerator $urlGenerator)
    {
        $this->producer = $producer;
        $this->consumer = $consumer;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws Exception
     */
    public function init(): void
    {
        if (!empty($this->getArgv())) {
            if (isset($this->getArgv()[1]) && $this->getArgv()[1] === 'producer' && $this->getCountUrls() !== null) {
                $this->urlGenerator->generateUrls($this->getCountUrls());
                $this->producer->push();
            } else {
                $this->consumer->listen();
            }
        }
    }

    /**
     * @return array|null
     */
    public function getArgv(): array|null
    {
        return $this->argv;
    }


    /**
     * @param array|null $argv
     * @return $this
     */
    public function setArgv(array|null $argv): self
    {
        $this->argv = $argv;
        return $this;
    }

    private function getCountUrls()
    {
        return (int)$this->getArgv()[2];
    }
}
