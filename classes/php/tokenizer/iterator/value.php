<?php

namespace atoum\atoum\php\tokenizer\iterator;

use atoum\atoum\exceptions;

abstract class value implements \iterator, \countable
{
    protected ?self $parent = null;

    public function setParent(self $parent): static
    {
        if ($this->parent !== null) {
            throw new exceptions\runtime('Parent is already set');
        }

        $parent->append($this);

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function getRoot(): ?self
    {
        $root = null;

        $parent = $this->getParent();

        while ($parent !== null) {
            $root = $parent;

            $parent = $parent->getParent();
        }

        return $root;
    }

    abstract public function __toString(): string;
    abstract public function prev(): mixed;
    abstract public function end(): static;
    abstract public function append(self $value): static;
    abstract public function getValue(): mixed;
    abstract public function seek(int $key): static;
}
