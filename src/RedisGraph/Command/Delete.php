<?php
declare(strict_types=1);

namespace Redislabs\Module\RedisGraph\Command;

use Redislabs\Interfaces\CommandInterface;
use Redislabs\Command\CommandAbstract;

final class Delete extends CommandAbstract implements CommandInterface
{
    protected static $command = 'GRAPH.DELETE';

    private function __construct(string $name)
    {
        $this->arguments = [$name];
    }

    public static function createCommandWithArguments(string $name) : CommandInterface
    {
        return new self($name);
    }
}
