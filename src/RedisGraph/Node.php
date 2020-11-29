<?php

declare(strict_types=1);

namespace Redislabs\Module\RedisGraph;

final class Node
{
    private $label;
    private $properties;
    private $alias;

    public function __construct(?string $label = null, ?iterable $properties = null)
    {
        $this->label = $label;
        $this->properties = $properties;
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
        return $this->alias ?? randomString();
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
        $nodeString = '(';
        if ($this->alias !== null) {
            $nodeString .= $this->alias;
        }
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
            $props .= $propKey . ': ' . quotedString((string) $propValue);
        }
        return $props;
    }
}
