<?php

namespace atoum\mock\stream;

use
	atoum\test\adapter
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
		if ($this->methodName == 'dir_readdir' && $mixed instanceof \atoum\mock\stream\controller)
		{
			$mixed = $mixed->getBasename();
		}

		return parent::offsetSet($call, $mixed);
	}
}
