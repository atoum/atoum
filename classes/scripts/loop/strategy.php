<?php

namespace mageekguy\atoum\scripts\loop;

interface strategy
{
	public function runAgain(\mageekguy\atoum\scripts\runner $runner);
}
