<?php

namespace mageekguy\atoum\tools\diffs;

use
	mageekguy\atoum\tools,
	mageekguy\atoum\exceptions
;

class variable extends tools\diff
{
	public function __construct()
	{
		$this->expected = null;
		$this->actual = null;
	}

	public function setExpected($mixed)
	{
		return parent::setExpected(self::dumpAsString($mixed));
	}

	public function setActual($mixed)
	{
		return parent::setActual(self::dumpAsString($mixed));
	}

	public function make()
	{
		if ($this->expected === null)
		{
			throw new exceptions\runtime('Expected is undefined');
		}

		if ($this->actual === null)
		{
			throw new exceptions\runtime('Actual is undefined');
		}

		return parent::make();
	}

	protected static function dumpAsString($mixed)
	{
		ob_start();

		var_dump($mixed);

		return trim(ob_get_clean());
	}
}
