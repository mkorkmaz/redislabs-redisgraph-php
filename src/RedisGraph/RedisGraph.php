<?php

declare(strict_types=1);

namespace Redislabs\Module\RedisGraph;

use Redislabs\Interfaces\ModuleInterface;
use Redislabs\Module\ModuleTrait;
use Redislabs\Module\RedisGraph\Interfaces\QueryInterface;
use Redislabs\Module\RedisGraph\Command\Query;
use Redislabs\Module\RedisGraph\Command\Explain;
use Redislabs\Module\RedisGraph\Command\Delete;

class RedisGraph implements ModuleInterface
{
    use ModuleTrait;

    protected static string $moduleName = 'RedisGraph';


    public function rawQuery(QueryInterface $query)
    {
        return $this->runCommand(
            Query::createCommandWithArguments($query)
        );
    }

    public function query(QueryInterface $query): Result
    {
        $response = $this->rawQuery($query);

        return Result::createFromResponse($response);
    }

    public function delete(string $name): ?string
    {
        return $this->runCommand(
            Delete::createCommandWithArguments($name)
        );
    }

    public function explain(QueryInterface $query): string
    {
        $response =  $this->runCommand(
            Explain::createCommandWithArguments($query)
        );
        return implode(' ', $response);
    }

    public function commit(QueryInterface $query): Result
    {
        return $this->query($query);
    }
}
