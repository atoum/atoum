<?php

namespace mageekguy\atoum\tools\diffs;

use \mageekguy\atoum\tools;

class variable extends tools\diff
{
	public function __construct()
	{
		$this->reference = null;
		$this->data = null;
	}

	public function setReference($mixed)
	{
		return parent::setReference(var_export($mixed, true));
	}

	public function setData($mixed)
	{
		return parent::setData(var_export($mixed, true));
	}

	public function make()
	{
		if ($this->reference === null)
		{
			throw new \runtimeException('Reference is undefined');
		}

		if ($this->data === null)
		{
			throw new \runtimeException('Data is undefined');
		}

		return parent::make();
	}
}

?>
