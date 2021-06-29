<?php

declare(strict_types=1);

namespace Redislabs\Module\RedisGraph;

use Redislabs\Module\RedisGraph\Interfaces\QueryInterface;

class GraphConstructor
{
    private string $name;
    /** @var Node[]  */
    private array $nodes = [];
    /** @var Edge[]  */
    private array $edges = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addNode(Node $node): void
    {
        $this->nodes[] = $node;
    }

    public function addEdge(Edge $edge): void
    {
        $this->edges[] = $edge;
    }

    public function getCommitQuery(): QueryInterface
    {
        $query = 'CREATE ';
        foreach ($this->nodes as $index => $node) {
            $query .= $node->toString() . ', ';
        }
        $edgeCount = count($this->edges);
        foreach ($this->edges as $index => $edge) {
            $query .= $edge->toString();
            if ($index < $edgeCount - 1) {
                $query .= ', ';
            }
        }
        return new Query($this->name, $query);
    }

    public function getCommitQueryWithMerge(): QueryInterface
    {
        $query = '';
        foreach ($this->nodes as $index => $node) {
            $query .= $node->getQueryPredicate() . ' ' . $node->toString() . ' ';
        }
        foreach ($this->edges as $index => $edge) {
            $query .= $edge->getQueryPredicate() . ' ' . $edge->toString() . ' ';
        }
        return new Query($this->name, trim($query));
    }
}
