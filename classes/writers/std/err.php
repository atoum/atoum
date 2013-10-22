<?php

namespace atoum\writers\std;

use
	atoum,
	atoum\writers
;

class err extends writers\std
{
	protected function init()
	{
		if ($this->resource === null)
		{
			$resource = $this->adapter->fopen('php://stderr', 'w');

			if ($resource === false)
			{
				throw new exceptions\runtime('Unable to open php://stderr stream');
			}

			$this->resource = $resource;
		}

		return $this;
	}
}
