<?php

namespace mageekguy\atoum\report\fields\runner\tests;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

abstract class duration extends report\fields\runner
{
	protected $value = null;
	protected $testNumber = null;

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
		if ($event === atoum\runner::runStop)
		{
			$this->value = $runner->getScore()->getTotalDuration();
			$this->testNumber = $runner->getTestNumber();
		}

		return $this;
	}
}

?>
