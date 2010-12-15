<?php

namespace mageekguy\atoum\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class memory extends report\fields\test
{
	const titlePrompt = '=> ';

	protected $value = null;

	public function getValue()
	{
		return $this->value;
	}

	public function setWithTest(atoum\test $test, $event = null)
	{
		if ($event === atoum\test::runStop)
		{
			$this->value = $test->getScore()->getTotalMemoryUsage();
		}

		return $this;
	}

	public function __toString()
	{
		$string = self::titlePrompt;

		if ($this->value === null)
		{
			$string .= $this->locale->_('Memory usage: unknown.');
		}
		else
		{
			$string .= sprintf($this->locale->_('Memory usage: %4.2f Mb.'), $this->value / 1048576);
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>
