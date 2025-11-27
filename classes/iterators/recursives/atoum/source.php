<?php

namespace atoum\atoum\iterators\recursives\atoum;

use atoum\atoum\iterators;

class source implements \outerIterator
{
    protected string $pharDirectory = '';
    protected string $sourceDirectory = '';
    protected ?\recursiveIteratorIterator $innerIterator = null;

    public function __construct(string $sourceDirectory, ?string $pharDirectory = null)
    {
        $this->sourceDirectory = (string) $sourceDirectory;
        $this->pharDirectory = $pharDirectory === null ? '' : (string) $pharDirectory;
        $this->innerIterator = new \recursiveIteratorIterator(new iterators\filters\recursives\atoum\source($this->sourceDirectory));

        $this->innerIterator->rewind();
    }

    public function getSourceDirectory(): string
    {
        return $this->sourceDirectory;
    }

    public function getPharDirectory(): string
    {
        return $this->pharDirectory;
    }

    #[\ReturnTypeWillChange]
    public function getInnerIterator(): \recursiveIteratorIterator
    {
        return $this->innerIterator;
    }

    #[\ReturnTypeWillChange]
    public function current(): ?string
    {
        $current = $this->innerIterator->current();

        return $current === null ? null : (string) $current;
    }

    #[\ReturnTypeWillChange]
    public function key(): mixed
    {
        if ($this->pharDirectory === '') {
            return $this->innerIterator->key();
        }

        $current = $this->innerIterator->current();

        return $current === null ? null : preg_replace('#^(:[^:]+://)?' . preg_quote($this->sourceDirectory, '#') . '#', $this->pharDirectory, $current);
    }

    #[\ReturnTypeWillChange]
    public function next(): void
    {
        $this->innerIterator->next();
    }

    #[\ReturnTypeWillChange]
    public function rewind(): void
    {
        $this->innerIterator->rewind();
    }

    #[\ReturnTypeWillChange]
    public function valid(): bool
    {
        return $this->innerIterator->valid();
    }
}
