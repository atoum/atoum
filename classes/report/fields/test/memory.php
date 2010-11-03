<?php

namespace mageekguy\atoum\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class memory extends report\fields\test
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
			$this->value = $test->getScore()->getTotalMemoryUsage();
		}

		return $this;
	}

	public function toString()
	{
		return
			(
				$this->value === null ?
				$this->locale->_('Memory usage: unknown.') :
				sprintf($this->locale->_('Memory usage: %4.2f Mb.'), $this->value / 1048576)
			)
			. PHP_EOL
		;
	}

}

?>
