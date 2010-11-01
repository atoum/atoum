<?php

namespace mageekguy\atoum\report\fields\runner\durations;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class tests extends report\fields\runner
{
	protected $testsNumber = null;
	protected $duration = null;

	public function toString()
	{
		return
			$this->duration === null ?
			$this->locale->__('Total test duration: unknown.', 'Total tests duration: unknown.', $this->testsNumber) :
			sprintf(
					$this->locale->__('Total test duration: %s.', 'Total tests duration: %s.', $this->testsNumber),
					sprintf(
						$this->locale->__('%4.2f second', '%4.2f seconds', $this->duration),
						$this->duration
					)
			)
		;
	}

	public function getDuration()
	{
		return $this->duration;
	}

	public function getTestsNumber()
	{
		return $this->testsNumber;
	}

	public function setWithRunner(atoum\runner $runner)
	{
		$this->duration = $runner->getScore()->getTotalDuration();
		$this->testsNumber = $runner->getTestsNumber();

		return $this;
	}
}

?>
