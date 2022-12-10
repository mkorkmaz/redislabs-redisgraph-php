<?php

declare(strict_types=1);

namespace Redislabs\Module\RedisGraph;

use SevenEcks\Tableify\Tableify;

class Result
{
    private array $statistics;

    public function __construct(private ?array $labels, private ?array $resultSet, Statistics $statistics)
    {
        $this->statistics = $statistics->getResultStatistics();
    }

    public static function createFromResponse(array $response): self
    {
        $stats = $response[2] ?? $response[0];
        $resultKeys = isset($response[1]) ? $response[0] : [];
        $resultSet = $response[1] ?? [];
        return new self($resultKeys, $resultSet, new Statistics($stats));
    }

    public function getResultSet(): array
    {
        return $this->resultSet;
    }
    public function getLabels(): array
    {
        return $this->labels;
    }


    public function getLabelsAdded(): int
    {
        return $this->statistics['LABELS_ADDED'];
    }

    public function getNodesCreated(): int
    {
        return $this->statistics['NODES_CREATED'];
    }

    public function getNodesDeleted(): int
    {
        return $this->statistics['NODES_DELETED'];
    }

    public function getRelationshipsCreated(): int
    {
        return $this->statistics['RELATIONSHIPS_CREATED'];
    }

    public function getRelationshipsDeleted(): int
    {
        return $this->statistics['RELATIONSHIPS_DELETED'];
    }

    public function getExecutionTime(): float
    {
        return $this->statistics['INTERNAL_EXECUTION_TIME'];
    }

    public function getPropertiesSet(): int
    {
        return $this->statistics['PROPERTIES_SET'];
    }
    public function getCachedExecution(): int
    {
        return $this->statistics['CACHED_EXECUTION'];
    }

    public function prettyPrint(): void
    {
        $table = Tableify::new(array_merge([$this->labels], $this->resultSet));
        $table = $table->make();
        $tableData = $table->toArray();
        foreach ($tableData as $row) {
            echo $row . "\n";
        }
    }
}
