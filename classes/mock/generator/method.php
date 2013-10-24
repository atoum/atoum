<?php

namespace mageekguy\atoum\mock\generator;

use
	mageekguy\atoum\mock
;

class method
{
	protected $name = null;
	protected $mockGenerator = null;

	public function __construct(mock\generator $mockGenerator = null, $name = null)
	{
		$this->setMockGenerator($mockGenerator);

		if ($name !== null)
		{
			$this->setName($name);
		}
	}

	public function __get($name)
	{
		return $this->setName($name);
	}

	public function __set($name, $mixed)
	{
		$this
			->setName($name)
			->getNextMockController()
				->{$this->name} = $mixed
		;

		return $this;
	}

	public function setName($name)
	{
		$this->name = (string) $name;

		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setMockGenerator(mock\generator $mockGenerator = null)
	{
		$this->mockGenerator = $mockGenerator ?: new mock\generator();

		return $this;
	}

	public function getMockGenerator()
	{
		return $this->mockGenerator;
	}

	public function canHaveNoArgument()
	{
		$this->checkName()->mockGenerator->orphanize($this->name);

		return $this;
	}

	public function canNotCallItsParent()
	{
		$this->checkName()->mockGenerator->shunt($this->name);

		return $this;
	}

	public function doesNothing()
	{
		$this
			->checkName()
			->getNextMockController()
				->{$this->name}->doesNothing()
		;

		return $this;
	}

	protected function checkName()
	{
		if ($this->name === null)
		{
			throw new method\exception('Method name is undefined');
		}

		return $this;
	}

	protected function getNextMockController()
	{
		$controller = mock\controller::get(false);

		if ($controller === null)
		{
			$controller = new mock\controller();
		}

		return $controller;
	}
}
