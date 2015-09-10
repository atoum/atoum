<?php

namespace mageekguy\atoum\test\dataProvider;

use mageekguy\atoum\test\dataProvider;

class set extends aggregator
{
	protected $aggregator;
	protected $size;

	public function __construct(aggregator $aggregator, $size = null)
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

		return array_map(function() use ($aggregator) { return $aggregator->generate(); }, range(0, $this->size));
	}

	public function count()
	{
		return $this->size;
	}
}
