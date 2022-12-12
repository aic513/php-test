<?php

namespace Src;

use Exception;

class App
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @throws Exception
     */
    public function run(array|null $argv): void
    {
        $this->router->setArgv($argv)->init();
    }
}
