<?php
declare(strict_types=1);

namespace Redislabs\Module\RedisGraph;

use SevenEcks\Tableify\Tableify;

class Result
{
    private $resultSet;
    private $statistics;

    public function __construct(array $resultSet, Statistics $statistics)
    {
        $this->resultSet = $resultSet;
        $this->statistics = $statistics->getResultStatistics();
    }

    public static function createFromResponse(array $response) : self
    {
        return new self($response[0], new Statistics($response[1]));
    }

    public function getResultSet() : array
    {
        return $this->resultSet;
    }

    public function getLabelsAdded() : int
    {
        return $this->statistics['LABELS_ADDED'];
    }

    public function getNodesCreated() : int
    {
        return $this->statistics['NODES_CREATED'];
    }

    public function getNodesDeleted() : int
    {
        return $this->statistics['NODES_DELETED'];
    }

    public function getRelationshipsCreated() : int
    {
        return $this->statistics['RELATIONSHIPS_CREATED'];
    }

    public function getRelationshipsDeleted() : int
    {
        return $this->statistics['RELATIONSHIPS_DELETED'];
    }

    public function getExecutionTime() : float
    {
        return $this->statistics['INTERNAL_EXECUTION_TIME'];
    }

    public function getPropertiesSet() : int
    {
        return $this->statistics['PROPERTIES_SET'];
    }

    public function prettyPrint() :void
    {
        $table = Tableify::new($this->resultSet);
        $table = $table->make();
        $tableData = $table->toArray();
        foreach ($tableData as $row) {
            echo $row . "\n";
        }
    }
}
