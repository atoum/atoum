<?php

namespace mageekguy\atoum\scripts\loop\strategy;

use mageekguy\atoum\scripts\loop\strategy;
use mageekguy\atoum\scripts\runner;

class prompt implements strategy
{
	public function runAgain(runner $runner)
	{
		return ($runner->prompt($runner->getLocale()->_('Press <Enter> to reexecute, press any other key and <Enter> to stop...')) == '');
	}
}
