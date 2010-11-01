<?php

namespace mageekguy\atoum\report\fields\runner\tests;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class memory extends report\fields\runner
{
	protected $value = null;
	protected $testsNumber = null;

	public function toString()
	{
		return
			$this->value === null ?
			$this->locale->__('Total test memory usage: unknown.', 'Total tests memory usage: unknown.', $this->testsNumber) :
			sprintf(
					$this->locale->__('Total test memory usage: %s.', 'Total tests memory usage: %s.', $this->testsNumber),
					sprintf(
						$this->locale->__('%4.2f Mb', '%4.2f Mb', $this->value / 1048576),
						$this->value / 1048576
					)
			)
		;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getTestsNumber()
	{
		return $this->testsNumber;
	}

	public function setWithRunner(atoum\runner $runner)
	{
		$this->value = $runner->getScore()->getTotalMemoryUsage();
		$this->testsNumber = $runner->getTestsNumber();

		return $this;
	}
}

?>
