<?php

namespace mageekguy\atoum\iterators\recursives\atoum;

use
	mageekguy\atoum\iterators
;

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

	public function getInnerIterator()
	{
		return $this->innerIterator;
	}

	public function current()
	{
		$current = $this->innerIterator->current();

		return $current === null ? null : (string) $current;
	}

	public function key()
	{
		return ($this->pharDirectory === null ? $this->innerIterator->key() : preg_replace('#^(:[^:]+://)?' . preg_quote($this->sourceDirectory, '#') . '#', $this->pharDirectory, $this->innerIterator->current()) ?: null);
	}

	public function next()
	{
		return $this->innerIterator->next();
	}

	public function rewind()
	{
		return $this->innerIterator->rewind();
	}

	public function valid()
	{
		return $this->innerIterator->valid();
	}
}
