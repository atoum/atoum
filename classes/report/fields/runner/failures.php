<?php

namespace mageekguy\atoum\report\fields\runner;

use
	\mageekguy\atoum\runner,
	\mageekguy\atoum\report
;

abstract class failures extends report\fields\runner
{
	protected $runner = null;

	public function getRunner()
	{
		return $this->runner;
	}

	public function setWithRunner(runner $runner, $event = null)
	{
		if ($this->runner !== $runner)
		{
			$this->runner = $runner;
		}

		return $this;
	}
}

?>
