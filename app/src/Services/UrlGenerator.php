<?php

namespace Src\Services;

use Faker\Factory as Faker;

class UrlGenerator
{
    public $faker;

    private array $urls;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function generateUrls(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->urls[] = $this->faker->url();
        }
    }

    /**
     * @return array
     */
    public function getUrls(): array
    {
        return $this->urls;
    }
}
