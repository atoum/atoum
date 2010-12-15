<?php

namespace mageekguy\atoum\report\fields\runner\tests;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class memory extends report\fields\runner
{
	const titlePrompt = '> ';

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

	public function __toString()
	{
		$string = self::titlePrompt;

		if ($this->value === null)
		{
			$string .= $this->locale->_('Total test memory usage: unknown.');
		}
		else
		{
			$string .= sprintf($this->locale->__('Total test memory usage: %4.2f Mb.', 'Total tests memory usage: %4.2f Mb.', $this->testNumber), $this->value / 1048576);
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>
