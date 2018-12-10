<?php
declare(strict_types=1);

namespace Redislabs\Module\RedisGraph\Command;

use Redislabs\Interfaces\CommandInterface;
use Redislabs\Command\CommandAbstract;
use Redislabs\Module\RedisGraph\Interfaces\QueryInterface;

final class Query extends CommandAbstract implements CommandInterface
{
    protected static $command = 'GRAPH.QUERY';

    private function __construct(QueryInterface $query)
    {
        $this->arguments = [$query->getName(), $query->getQueryString()];
    }

    public static function createCommandWithArguments(QueryInterface $query) : CommandInterface
    {
        return new self($query);
    }
}
