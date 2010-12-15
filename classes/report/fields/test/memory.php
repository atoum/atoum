<?php

namespace mageekguy\atoum\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

abstract class memory extends report\fields\test
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
}

?>
