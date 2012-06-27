<?php

namespace mageekguy\atoum\score\coverage;

use
	mageekguy\atoum\score
;

class container
{
	protected $classes = array();
	protected $methods = array();

	public function __construct(score\coverage $coverage)
	{
		$this->classes = $coverage->getClasses();
		$this->methods = $coverage->getMethods();
	}

	public function getClasses()
	{
		return $this->classes;
	}

	public function getMethods()
	{
		return $this->methods;
	}
}
