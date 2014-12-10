<?php

namespace mageekguy\atoum\factory\builder;

use
	mageekguy\atoum\test,
	mageekguy\atoum\factory
;

class closure implements factory\builder
{
	private $factory = null;
	private $allArgumentsAreOptional = true;

	public function build(\reflectionClass $class, & $instance = null)
	{
		$this->factory = null;

		if ($class->isInterface() === false && $class->isAbstract() === false)
		{
			$constructor = $class->getConstructor();

			if ($constructor === null || $constructor->isPublic() === true)
			{
				$constructorParameters = $closureParameters = array();

				if ($constructor !== null)
				{
					$this->allArgumentsAreOptional = ($constructor->getNumberOfRequiredParameters() === 0);

					foreach ($constructor->getParameters() as $position => $parameter)
					{
						$closureParameters[$position] = ($parameter->isPassedByReference() === false ? '' : '& ') . $constructorParameters[$position] = '$' . $parameter->getName();

						if (self::isVariadic($parameter))
						{
							$closureParameters[$position] = '...' . $closureParameters[$position];
							$constructorParameters[$position] = '...' . $constructorParameters[$position];
						}

						switch (true)
						{
							case $parameter->isDefaultValueAvailable():
								$defaultValue = var_export($parameter->getDefaultValue(), true);
								break;

							case $parameter->isOptional() && self::isVariadic($parameter) === false:
								$defaultValue = 'null';
								break;

							default:
								$defaultValue = null;
						}

						if ($defaultValue !== null)
						{
							$closureParameters[$position] .= ' = ' . $defaultValue;
						}
					}
				}

				if ($constructor === null || sizeof($closureParameters) <= 0)
				{
					$this->factory = function() use (& $instance, $class) { return ($instance = $class->newInstanceArgs(func_get_args())); };
				}
				else
				{
					$this->factory = eval('return function(' . join(', ', $closureParameters) . ') use (& $instance) { return ($instance = new ' . $class->getName() . '(' . join(', ', $constructorParameters) . ')); };');
				}
			}
		}

		return $this;
	}

	public function get()
	{
		return $this->factory;
	}

	public function addToAssertionManager(test\assertion\manager $assertionManager, $factoryName, $defaultHandler)
	{
		if ($this->factory === null)
		{
			$assertionManager->setHandler($factoryName, $defaultHandler);
		}
		else
		{
			$assertionManager
				->setMethodHandler($factoryName, $this->factory)
				->setPropertyHandler($factoryName, $this->allArgumentsAreOptional === false ? $defaultHandler : $this->factory)
			;
		}

		return $this;
	}

	private static function isVariadic(\reflectionParameter $parameter)
	{
		return (method_exists($parameter, 'isVariadic') && $parameter->isVariadic());
	}
}
