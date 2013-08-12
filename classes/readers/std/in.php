<?php

namespace mageekguy\atoum\readers\std;

use
	mageekguy\atoum\reader,
	mageekguy\atoum\exceptions
;

class in extends reader
{
	protected $resource = null;

	public function read($length = null)
	{
		$this->init();

		// fgets() must be called without any second argument if $length is null to avoid message "Warning: fgets(): Length parameter must be greater than 0"
		return ($length === null ? $this->adapter->fgets($this->resource) : $this->adapter->fgets($this->resource, $length));
	}

	protected function init()
	{
		if ($this->resource === null)
		{
			$resource = $this->adapter->fopen('php://stdin', 'r');

			if ($resource === false)
			{
				throw new exceptions\runtime('Unable to open php://stdin stream');
			}

			$this->resource = $resource;
		}

		return $this;
	}
}
