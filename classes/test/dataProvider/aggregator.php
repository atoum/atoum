<?php

namespace mageekguy\atoum\test\dataProvider;

use mageekguy\atoum\test\dataProvider;

class aggregator implements dataProvider, \countable
{
	protected $providers = array();

	public function __invoke()
	{
		return $this->generate();
	}

	public function __toString()
	{
		return __CLASS__ . '<' . implode(', ', array_map(function(dataProvider $provider) { return $provider->__toString(); }, $this->providers)) . '>';
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

	public function addProvider(dataProvider $provider)
	{
		$this->providers[] = $provider;

		return $this;
	}

	public function count()
	{
		return sizeof($this->providers);
	}
}
