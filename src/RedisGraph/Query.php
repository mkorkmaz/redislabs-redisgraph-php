<?php

declare(strict_types=1);

namespace Redislabs\Module\RedisGraph;

use Redislabs\Module\RedisGraph\Interfaces\QueryInterface;

final class Query implements QueryInterface
{
    private $name;
    private $queryString;

    public function __construct(string $name, string $queryString)
    {
        $this->name = $name;
        $this->queryString = $queryString;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }
}
