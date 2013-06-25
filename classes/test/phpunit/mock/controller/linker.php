<?php

namespace mageekguy\atoum\test\phpunit\mock\controller;

use
	mageekguy\atoum,
	mageekguy\atoum\mock
;

class linker extends atoum\mock\controller\linker
{
	protected $test;

	public function __construct(atoum\test $test)
	{
		$this->test = $test;

		parent::__construct();
	}

	public function reset()
	{
		foreach ($this->mocks as $controller)
		{
			$mock = $this->getMock($controller);
			$mock->getMockDefinition()->verdict($this->test);

			$controller->reset();
		}

		return $this->init();
	}
}
