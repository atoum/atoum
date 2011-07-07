<?php

namespace mageekguy\atoum\report\fields\runner;

use
	\mageekguy\atoum\runner,
	\mageekguy\atoum\report
;

abstract class duration extends report\fields\runner
{
	protected $value = null;

	public function getValue()
	{
		return $this->value;
	}

	public function setWithRunner(runner $runner, $event = null)
	{
		if ($event === runner::runStop)
		{
			$this->value = $runner->getRunningDuration();
		}

		return $this;
	}
}

?>
