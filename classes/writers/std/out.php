<?php

namespace atoum\writers\std;

use
	atoum,
	atoum\writers
;

class out extends writers\std
{
	protected function init()
	{
		if ($this->resource === null)
		{
			$resource = $this->adapter->fopen('php://stdout', 'w');

			if ($resource === false)
			{
				throw new exceptions\runtime('Unable to open php://stout stream');
			}

			$this->resource = $resource;
		}

		return $this;
	}
}
