<?php

namespace mageekguy\atoum\writers\std;

use
	mageekguy\atoum,
	mageekguy\atoum\writers
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
				throw new exceptions\runtime('Unable to open php://stdout stream');
			}

			$this->resource = $resource;
		}

		return $this;
	}
}
