<?php

namespace mageekguy\atoum\observers;

use \mageekguy\atoum;

interface runner extends atoum\observer
{
	public function runnerStart(atoum\runner $runner);
	public function runnerStop(atoum\runner $runner);
}

?>
