<?php

declare(strict_types=1);

namespace Redislabs\Module\RedisGraph;

use Redislabs\Module\RedisGraph\Interfaces\QueryInterface;

final class Query implements QueryInterface
{
    public function __construct(private string $name, private string $queryString)
    {
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
