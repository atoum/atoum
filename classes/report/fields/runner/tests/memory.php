<?php

namespace mageekguy\atoum\report\fields\runner\tests;

use
	mageekguy\atoum,
	mageekguy\atoum\report
;

abstract class memory extends report\fields\runner
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
			$this->value = $runner->getScore()->getTotalMemoryUsage();
			$this->testNumber = $runner->getTestNumber();
		}

		return $this;
	}
}

?>
