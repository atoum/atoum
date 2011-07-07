<?php

namespace mageekguy\atoum\report\fields\runner;

use
	\mageekguy\atoum\runner,
	\mageekguy\atoum\report
;

abstract class coverage extends report\fields\runner
{
	protected $coverage = null;

	public function getCoverage()
	{
		return $this->coverage;
	}

	public function setWithRunner(runner $runner, $event = null)
	{
		if ($event === runner::runStop)
		{
			$this->coverage = $runner->getScore()->getCoverage();
		}

		return $this;
	}
}

?>
