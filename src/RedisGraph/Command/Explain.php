<?php

declare(strict_types=1);

namespace Redislabs\Module\RedisGraph\Command;

use Redislabs\Interfaces\CommandInterface;
use Redislabs\Command\CommandAbstract;
use Redislabs\Module\RedisGraph\Interfaces\QueryInterface;

final class Explain extends CommandAbstract implements CommandInterface
{
    protected static $command = 'GRAPH.EXPLAIN';

    private function __construct(string $name, string $queryString)
    {
        $this->arguments = [$name, $queryString, '--compact'];
    }

    public static function createCommandWithArguments(QueryInterface $query): CommandInterface
    {
        return new self($query->getName(), $query->getQueryString());
    }
}
