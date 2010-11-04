<?php

namespace mageekguy\atoum\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class output extends report\fields\test
{
	protected $test = null;

	public function getTest()
	{
		return $this->test;
	}

	public function setWithTest(atoum\test $test, $event = null)
	{
		if ($this->test !== $test)
		{
			$this->test = $test;
		}

		return $this;
	}

	public function toString()
	{
		$string = '';

		if ($this->test !== null)
		{
			$outputs = $this->test->getScore()->getOutputs();

			if (sizeof($outputs) > 0)
			{
				$string .= $this->locale->_('Outputs:') . PHP_EOL;
			}
		}

		return $string;
	}
}

?>
