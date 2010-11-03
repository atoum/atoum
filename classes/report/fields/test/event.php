<?php

namespace mageekguy\atoum\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class event extends report\fields\test
{
	protected $value = null;

	public function setWithTest(atoum\test $test, $event = null)
	{
		$this->value = $event;

		return $this;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function toString()
	{
		return '';
	}
}

?>
