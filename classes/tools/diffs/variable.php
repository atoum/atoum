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
		$this->reference = null;
		$this->data = null;
	}

	public function setReference($mixed)
	{
		return parent::setReference(self::dumpAsString($mixed));
	}

	public function setData($mixed)
	{
		return parent::setData(self::dumpAsString($mixed));
	}

	public function make()
	{
		if ($this->reference === null)
		{
			throw new exceptions\runtime('Reference is undefined');
		}

		if ($this->data === null)
		{
			throw new exceptions\runtime('Data is undefined');
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
