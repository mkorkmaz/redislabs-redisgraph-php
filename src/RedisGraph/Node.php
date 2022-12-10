<?php

declare(strict_types=1);

namespace Redislabs\Module\RedisGraph;

final class Node
{
    private string $alias;

    public function __construct(private ?string $label = null, private ?iterable $properties = null)
    {
        $this->alias = randomString();
    }

    public static function create(): self
    {
        return new self();
    }

    public static function createWithLabel(string $label): self
    {
        return new self($label);
    }

    public static function createWithLabelAndProperties(string $label, iterable $properties): self
    {
        return new self($label, $properties);
    }

    public function withLabel(string $label): self
    {
        $new = clone $this;
        $new->label = $label;
        return $new;
    }

    public function withProperties(iterable $properties): self
    {
        $new = clone $this;
        $new->properties = $properties;
        return $new;
    }

    public function withAlias(string $alias): self
    {
        $new = clone $this;
        $new->alias = $alias;
        return $new;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getProperties(): iterable
    {
        return $this->properties;
    }

    public function toString(): string
    {
        $nodeString = '(' . $this->getAlias();
        if ($this->label !== null) {
            $nodeString .= ':' . $this->label . ' ';
        }
        if ($this->properties !== null) {
            $nodeString .= '{' . $this->getProps($this->properties) . '}';
        }
        $nodeString .= ')';
        return $nodeString;
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
        if ($type === 'double') {
            return (double) $propValue;
        }
        return (int) $propValue;
    }
}
