<?php

namespace mageekguy\atoum\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class duration extends report\fields\test
{
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
		return
			(
				$this->value === null ?
				$this->locale->_('Test duration: unknown.') :
				sprintf($this->locale->__('Test duration: %4.2f second.', 'Test duration: %4.2f seconds.', $this->value), $this->value)
			)
			. PHP_EOL
		;
	}
}

?>
