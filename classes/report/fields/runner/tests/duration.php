<?php

namespace mageekguy\atoum\report\fields\runner\tests;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class duration extends report\fields\runner
{
	protected $value = null;
	protected $testNumber = null;

	public function toString()
	{
		return
			$this->value === null ?
			$this->locale->__('Total test duration: unknown.', 'Total tests duration: unknown.', $this->testNumber) :
			sprintf(
					$this->locale->__('Total test duration: %s.', 'Total tests duration: %s.', $this->testNumber),
					sprintf(
						$this->locale->__('%4.2f second', '%4.2f seconds', $this->value),
						$this->value
					)
			)
		;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getTestNumber()
	{
		return $this->testNumber;
	}

	public function setWithRunner(atoum\runner $runner, $event = null)
	{
		$this->value = $runner->getScore()->getTotalDuration();
		$this->testNumber = $runner->getTestNumber();

		return $this;
	}
}

?>
