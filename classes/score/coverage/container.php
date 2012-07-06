<?php

namespace mageekguy\atoum\score\coverage;

use
	mageekguy\atoum\score
;

class container
{
	protected $classes = array();
	protected $methods = array();

	public function getClasses()
	{
		return $this->classes;
	}

	public function getMethods()
	{
		return $this->methods;
	}

	public function merge(container $container)
	{
		$this->classes = array_merge($this->classes, $container->getClasses());
		$this->methods = array_merge($this->methods, $container->getMethods());

		return $this;
	}
}
