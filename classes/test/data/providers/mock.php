<?php

namespace mageekguy\atoum\test\data\providers;

use
	mageekguy\atoum\exceptions\logic,
	mageekguy\atoum\mock\generator,
	mageekguy\atoum\test\data\provider,
	mageekguy\atoum\exceptions\runtime
;

class mock extends object
{
	private $mockGenerator;

	public function __construct(generator $mockGenerator = null)
	{
		$this->setMockGenerator($mockGenerator);
	}

	public function __toString()
	{
		return __CLASS__ . '<' . $this->class . '>';
	}

	public function __wakeup()
	{
		$this->setMockGenerator();
	}

	public function getMockGenerator()
	{
		return $this->mockGenerator;
	}

	public function setMockGenerator(generator $mockGenerator = null)
	{
		$this->mockGenerator = $mockGenerator ?: new generator();
	}

	public function generate()
	{
		$mockNamespace = $this->mockGenerator->getDefaultNamespace();
		$className = $mockNamespace . '\\' . $this->classIsSet()->class;

		if (static::classExists($className) === false)
		{
			$this->mockGenerator->generate($this->class);
		}

		try
		{
			if (static::canInstanciateClass($className))
			{
				return new $className();
			}
		}
		catch (provider\object\exceptions\privateConstructor $exception)
		{
			throw new provider\object\exceptions\privateConstructor('Could not instanciate a mock from ' . $mockNamespace . '\\' . $this->class . ' because ' . $this->class . '::__construct() is private');
		}
		catch (provider\object\exceptions\mandatoryArgument $exception)
		{
			throw new provider\object\exceptions\mandatoryArgument('Could not instanciate a mock from ' . $mockNamespace . '\\' . $this->class . ' because ' . $this->class . '::__construct() has at least one mandatory argument');
		}


		throw new runtime('Could not instanciate a mock from ' . $this->class);
	}

	protected static function classExists($class)
	{
		return parent::classExists($class) || interface_exists($class);
	}
}
