<?php
namespace mageekguy\atoum\test\phpunit\asserter;

use mageekguy\atoum;

class selector
{
	public function selectFor($value, atoum\asserter $asserter = null)
	{
		$asserter = $asserter ?: $this->selectAsserter($value);

		return $asserter->setWith($value);
	}

	protected function selectAsserter($value)
	{
		return new atoum\asserters\variable();
	}
} 