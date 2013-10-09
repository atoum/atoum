<?php

namespace mageekguy\atoum\test\adapter\calls;

use
	mageekguy\atoum\test\adapter\calls
;

class decorator
{
	public function decorate(calls $calls)
	{
		$string = '';

		$sizeOfCalls = sizeof($calls);

		if ($sizeOfCalls > 0)
		{
			$format = '[%' . strlen((string) $sizeOfCalls) . 's] %s';

			$position = 1;

			foreach ($calls->getTimeline() as $call)
			{
				$string .= sprintf($format, $position++, $call) . PHP_EOL;
			}
		}

		return $string;
	}
}
