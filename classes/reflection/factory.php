<?php

namespace mageekguy\atoum\reflection;

use
	atoum\test
;

class factory
{
	private $factory = null;
	private $allArgumentsAreOptional = false;

	public function build(\reflectionClass $class, & $instance = null)
	{
		$this->factory = null;
		$numberOfDefaultArgument = 0;

		if ($class->isInterface() === false && $class->isAbstract() === false)
		{
			$constructor = $class->getConstructor();

			if ($constructor === null || $constructor->isPublic() === true)
			{
				$constructorParameters = $closureParameters = array();

				if ($constructor !== null)
				{
					foreach ($constructor->getParameters() as $position => $parameter)
					{
						$closureParameters[$position] = $constructorParameters[$position] = '$' . $parameter->getName();

						if ($parameter->isPassedByReference() === true)
						{
							$closureParameters[$position] = '& ' . $closureParameters[$position];
						}

						switch (true)
						{
							case $parameter->isDefaultValueAvailable():
								$defaultValue = var_export($parameter->getDefaultValue(), true);
								break;

							case $parameter->isOptional():
								$defaultValue = 'null';
								break;

							default:
								$defaultValue = null;
						}

						if ($defaultValue !== null)
						{
							$closureParameters[$position] .= ' = ' . $defaultValue;
							$numberOfDefaultArgument++;
						}
					}
				}

				if ($constructor === null || sizeof($closureParameters) <= 0)
				{
					$factoryCode = 'function() use (& $instance, $class) { return ($instance = $class->newInstanceArgs(func_get_args())); };';
				}
				else
				{
					$factoryCode = 'function(' . join(', ', $closureParameters) . ') use (& $instance) { return ($instance = new ' . $class->getName() . '(' . join(', ', $constructorParameters) . ')); };';
				}

				if ($numberOfDefaultArgument === sizeof($closureParameters))
				{
					$this->allArgumentsAreOptional = true;
				}

				$this->factory = eval('return ' . $factoryCode);
			}
		}

		return $this;
	}

	public function get()
	{
		return $this->factory;
	}

	public function addToAssertionManager(test\assertion\manager $assertionManager, $factoryName)
	{
		if ($this->factory !== null)
		{
			$assertionManager->setMethodHandler($factoryName, $this->factory);

			if ($this->allArgumentsAreOptional === true)
			{
				$assertionManager->setPropertyHandler($factoryName, $this->factory);
			}
		}

		return $this;
	}
}
