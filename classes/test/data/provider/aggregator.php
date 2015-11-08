<?php

namespace mageekguy\atoum\test\data\provider;

use mageekguy\atoum\test\data\provider;

class aggregator implements provider, \countable
{
	protected $providers = array();

	public function __invoke()
	{
		return $this->generate();
	}

	public function __toString()
	{
		$types = array_map(
			function(provider $provider) {
				return $provider->__toString();
			},
			$this->providers
		);

		return __CLASS__ . '<' . implode(', ', $types) . '>';
	}

	public function generate()
	{
		$data = array();

		foreach ($this->providers as $provider)
		{
			$data[] = $provider->generate();
		}

		return $data;
	}

	public function addProvider(provider $provider)
	{
		$this->providers[] = $provider;

		return $this;
	}

	public function count()
	{
		return sizeof($this->providers);
	}
}
