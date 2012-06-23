<?php

namespace mageekguy\atoum\test;

use
	mageekguy\atoum
;

abstract class engine
{
	public function __construct(atoum\factory $factory = null)
	{
		$this->setFactory($factory ?: new atoum\factory());
	}

	public function setFactory(atoum\factory $factory)
	{
		$this->factory = $factory;

		return $this;
	}

	public function getFactory()
	{
		return $this->factory;
	}

	public abstract function isAsynchronous();
	public abstract function run(atoum\test $test);
	public abstract function getScore();
}
