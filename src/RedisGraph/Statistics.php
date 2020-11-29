<?php

declare(strict_types=1);

namespace Redislabs\Module\RedisGraph;

class Statistics
{
    private $statistics = [
        'LABELS_ADDED' => 0,
        'NODES_CREATED' => 0,
        'NODES_DELETED' => 0,
        'RELATIONSHIPS_CREATED' => 0,
        'RELATIONSHIPS_DELETED' => 0,
        'PROPERTIES_SET' => 0,
        'CACHED_EXECUTION' => 0,
        'INTERNAL_EXECUTION_TIME' => '0.0'
    ];
    private static $availableStatistics = [
        'Labels added' => 'LABELS_ADDED',
        'Nodes created' => 'NODES_CREATED',
        'Nodes deleted' => 'NODES_DELETED',
        'Cached execution' => 'CACHED_EXECUTION',
        'Relationships deleted' => 'RELATIONSHIPS_DELETED',
        'Properties set' => 'PROPERTIES_SET',
        'Relationships created' => 'RELATIONSHIPS_CREATED',
        'Query internal execution time' => 'INTERNAL_EXECUTION_TIME',
    ];

    public function __construct(array $statistics)
    {
        foreach ($statistics as $stat) {
            $statDetails = $this->getStat($stat);
            if ($statDetails !== null) {
                $this->statistics[$statDetails['key']] = $statDetails['value'];
            }
        }
    }

    private function getStat($stat): array
    {

        $statDetails = explode(':', $stat);
        $statName = self::$availableStatistics[$statDetails[0]];
        if ($statName === 'INTERNAL_EXECUTION_TIME') {
            return ['key' => $statName, 'value' => (float) $statDetails[1]];
        }
        return ['key' => $statName, 'value' => (int) $statDetails[1]];
    }

    public function getResultStatistics(): array
    {
        return $this->statistics;
    }
}
