<?php

namespace mageekguy\atoum\mock\stream;

use
	mageekguy\atoum\test\adapter
;

class invoker extends adapter\invoker
{
	protected $methodName = '';

	public function __construct($methodName)
	{
		$this->methodName = strtolower($methodName);
	}

	public function getMethodName()
	{
		return $this->methodName;
	}

	public function offsetSet($call, $mixed)
	{
		switch ($this->methodName)
		{
			case 'dir_readdir':
				if ($mixed instanceof \mageekguy\atoum\mock\stream\controller)
				{
					$mixed = $mixed->getBasename();
				}
				break;
		}

		return parent::offsetSet($call, $mixed);
	}
}

?>
