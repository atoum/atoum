<?php

namespace mageekguy\atoum\test\adapter\call\decorators;

use
	mageekguy\atoum\test\adapter\call
;

class addClass extends call\decorator
{
	protected $class = '';

	public function __construct($mixed)
	{
		parent::__construct();

		$this->class = (is_object($mixed) === false ? (string) $mixed : get_class($mixed));
	}

	public function getClass()
	{
		return $this->class;
	}

	public function decorate(call $call)
	{
		$string = parent::decorate($call);

		if ($string !== '')
		{
			$string = $this->class . '::' . $string;
		}

		return $string;
	}
}
