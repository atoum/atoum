<?php

namespace mageekguy\atoum\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

abstract class duration extends report\fields\runner
{
	protected $value = null;

	public function getValue()
	{
		return $this->value;
	}

	public function setWithRunner(atoum\runner $runner, $event = null)
	{
		if ($event === atoum\runner::runStop)
		{
			$this->value = $runner->getRunningDuration();
		}

		return $this;
	}
}

?>
