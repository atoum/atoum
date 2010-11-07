<?php

namespace mageekguy\atoum\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class duration extends report\fields\test
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
			$this->value = $test->getScore()->getTotalDuration();
		}

		return $this;
	}

	public function toString()
	{
		$string = self::titlePrompt;

		if ($this->value === null)
		{
			$string .= $this->locale->_('Test duration: unknown.');
		}
		else
		{
			$string .= sprintf($this->locale->__('Test duration: %4.2f second.', 'Test duration: %4.2f seconds.', $this->value), $this->value);
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>
