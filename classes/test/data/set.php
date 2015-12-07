<?php

namespace mageekguy\atoum\test\data;

use mageekguy\atoum\test\data\provider;

class set extends provider\aggregator
{
	protected $provider;
	protected $size;

	public function __construct(provider $provider, $size = null)
	{
		$this->provider = $provider;
		$this->size = $size ?: 1;
	}

	public function __invoke()
	{
		return $this->generate();
	}

	public function __toString()
	{
		return $this->provider->__toString();
	}

	public function generate()
	{
		$provider = $this->provider;

		return array_map(
			function() use ($provider) {
				return $provider->generate();
			},
			range(0, $this->size - 1)
		);
	}

	public function count()
	{
		return $this->size;
	}
}
