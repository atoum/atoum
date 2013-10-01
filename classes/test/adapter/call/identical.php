<?php

namespace mageekguy\atoum\test\adapter\call;

use
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\test\adapter\call
;

class identical extends call
{
	public function find(adapter $adapter)
	{
		$calls = parent::find($adapter);

		if (sizeof($calls) > 0 && $this->arguments !== null && $this->arguments !== array())
		{
			$arguments = $this->arguments;

			$filter = function($a) use ($arguments) {
				return ($a === $arguments);
			};

			$calls = array_filter($calls, $filter);
		}

		return $calls;
	}
}
