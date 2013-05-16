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
		return $this->init()->adapter->fgets($this->resource, $length);
	}

	protected function init()
	{
		if ($this->resource === null)
		{
			$resource = $this->adapter->fopen('php://stdout', 'w');

			if ($resource === false)
			{
				throw new exceptions\runtime('Unable to open php://stdin stream');
			}

			$this->resource = $resource;
		}

		return $this;
	}
}
