<?php

namespace atoum\atoum\iterators\recursives\atoum;

use atoum\atoum\iterators;

class source implements \outerIterator
{
    protected $pharDirectory = '';
    protected $sourceDirectory = '';
    protected $innerIterator = null;

    public function __construct($sourceDirectory, $pharDirectory = null)
    {
        $this->sourceDirectory = (string) $sourceDirectory;
        $this->pharDirectory = $pharDirectory === null ? null : (string) $pharDirectory;
        $this->innerIterator = new \recursiveIteratorIterator(new iterators\filters\recursives\atoum\source($this->sourceDirectory));

        $this->innerIterator->rewind();
    }

    public function getSourceDirectory()
    {
        return $this->sourceDirectory;
    }

    public function getPharDirectory()
    {
        return $this->pharDirectory;
    }

    #[\ReturnTypeWillChange]
    public function getInnerIterator()
    {
        return $this->innerIterator;
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        $current = $this->innerIterator->current();

        return $current === null ? null : (string) $current;
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        if ($this->pharDirectory === null) {
            return $this->innerIterator->key();
        }

        $current = $this->innerIterator->current();

        return $current === null ? null : preg_replace('#^(:[^:]+://)?' . preg_quote($this->sourceDirectory, '#') . '#', $this->pharDirectory, $current);
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        return $this->innerIterator->next();
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        return $this->innerIterator->rewind();
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return $this->innerIterator->valid();
    }
}
