<?php

namespace mageekguy\atoum\mock\generator;

use
	mageekguy\atoum\mock
;

class method
{
	protected $name = '';
	protected $generator = null;

	public function __construct($name, mock\generator $mockGenerator = null)
	{
		$this->name = $name;

		$this->setMockGenerator($mockGenerator);
	}

	public function setMockGenerator(mock\generator $mockGenerator = null)
	{
		$this->mockGenerator = $mockGenerator ?: new mock\generator();

		return $this;
	}

	public function canHaveNoArgument()
	{
		$this->mockGenerator->orphanize($this->name);

		return $this;
	}
}
