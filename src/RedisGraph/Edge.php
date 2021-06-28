<?php

declare(strict_types=1);

namespace Redislabs\Module\RedisGraph;

final class Edge
{

    private Node $sourceNode;
    private Node $destinationNode;
    private ?string $relation;
    private ?iterable $properties = [];

    public function __construct(Node $sourceNode, ?string $relation, Node $destinationNode, ?iterable $properties = [])
    {
        $this->sourceNode = $sourceNode;
        $this->destinationNode = $destinationNode;
        $this->relation = $relation;
        $this->properties = $properties;
    }

    public static function create(Node $sourceNode, string $relation, Node $destinationNode): self
    {
        return new self($sourceNode, $relation, $destinationNode);
    }

    public function withProperties(iterable $properties): self
    {
        $new = clone $this;
        $new->properties = $properties;
        return $new;
    }

    public function toString(): string
    {
        $edgeString = '(' . $this->sourceNode->getAlias() . ':' . $this->sourceNode->getLabel() . ')';
        $edgeString .= '-[';
        if ($this->relation !== null) {
            $edgeString .= ':' . $this->relation . ' ';
        }
        if ($this->properties) {
            $edgeString .= '{' . $this->getProps($this->properties) . '}';
        }
        $edgeString .= ']->';
        $edgeString .= '(' . $this->destinationNode->getAlias() . ':' . $this->destinationNode->getLabel() . ')';
        return $edgeString;
    }

    private function getProps(iterable $properties): string
    {
        $props = '';
        foreach ($properties as $propKey => $propValue) {
            if ($props !== '') {
                $props .= ', ';
            }
            $props .= $propKey . ': ' . $this->getPropValueWithDataType($propValue);
        }
        return $props;
    }

    private function getPropValueWithDataType($propValue)
    {
        $type = gettype($propValue);
        if ($type === 'string') {
            return quotedString((string) $propValue);
        }
        return (int) $propValue;
    }
}
