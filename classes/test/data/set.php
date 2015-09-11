<?php

namespace mageekguy\atoum\test\data;

use mageekguy\atoum\test\data\provider;

class set extends provider\aggregator
{
	protected $aggregator;
	protected $size;

	public function __construct(provider\aggregator $aggregator, $size = null)
	{
		$this->aggregator = $aggregator;
		$this->size = $size ?: 1;
	}

	public function __invoke()
	{
		return $this->generate();
	}

	public function __toString()
	{
		return $this->aggregator->__toString();
	}

	public function generate()
	{
		$aggregator = $this->aggregator;

		return array_map(function() use ($aggregator) { return $aggregator->generate(); }, range(0, $this->size - 1));
	}

	public function count()
	{
		return $this->size;
	}
}
