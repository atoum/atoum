<?php

namespace atoum\atoum\php\tokenizer;

use atoum\atoum\exceptions;

class token extends iterator\value
{
    protected ?int $key = 0;
    protected int|string $tag = '';
    protected ?string $string = null;
    protected ?int $line = null;

    public function __construct(int|string $tag, ?string $string = null, ?int $line = null, ?iterator\value $parent = null)
    {
        $this->tag = $tag;
        $this->string = $string;
        $this->line = $line;

        if ($parent !== null) {
            $this->setParent($parent);
        }
    }

    public function __toString(): string
    {
        return (string) ($this->string ?: $this->tag);
    }

    #[\ReturnTypeWillChange]
    public function count()
    {
        return 1;
    }

    public function getTag(): int|string
    {
        return $this->tag;
    }

    public function getString(): ?string
    {
        return $this->string;
    }

    public function getLine(): ?int
    {
        return $this->line;
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->key === 0 ? 0 : null;
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->key !== 0 ? null : $this;
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->key = 0;

        return $this;
    }

    public function end(): static
    {
        $this->key = 0;

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return ($this->key === 0);
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        if ($this->valid() === true) {
            $this->key = null;
        }

        return $this;
    }

    public function prev(): static
    {
        if ($this->valid() === true) {
            $this->key = null;
        }

        return $this;
    }

    public function append(iterator\value $value): static
    {
        throw new exceptions\logic(__METHOD__ . '() is unavailable');
    }

    public function seek(int $key): static
    {
        if ($key != 0) {
            $this->key = null;
        } else {
            $this->key = 0;
        }

        return $this;
    }

    public function getParent(): ?iterator\value
    {
        return $this->parent;
    }

    public function getValue(): ?string
    {
        return $this->getString();
    }
}
