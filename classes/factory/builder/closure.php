<?php

namespace mageekguy\atoum\factory\builder;

use
	mageekguy\atoum\test,
	mageekguy\atoum\factory
;

class closure implements factory\builder
{
	private $factory = null;
	private $allArgumentsAreOptional = false;

	public function build(\reflectionClass $class, & $instance = null)
	{
		$this->factory = null;

		if ($class->isInterface() === false && $class->isAbstract() === false)
		{
			$constructor = $class->getConstructor();

			if ($constructor === null || $constructor->isPublic() === true)
			{
				$numberOfDefaultArgument = 0;
				$constructorParameters = $closureParameters = array();

				if ($constructor !== null)
				{
					foreach ($constructor->getParameters() as $position => $parameter)
					{
						$closureParameters[$position] = ($parameter->isPassedByReference() === false ? '' : '& ') . $constructorParameters[$position] = '$' . $parameter->getName();

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
					$this->factory = function() use (& $instance, $class) { return ($instance = $class->newInstanceArgs(func_get_args())); };
				}
				else
				{
					$this->factory = eval('return function(' . join(', ', $closureParameters) . ') use (& $instance) { return ($instance = new ' . $class->getName() . '(' . join(', ', $constructorParameters) . ')); };');
				}

				$this->allArgumentsAreOptional = ($numberOfDefaultArgument === sizeof($closureParameters));
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
			$assertionManager
				->setMethodHandler($factoryName, $defaultHandler)
				->setPropertyHandler($factoryName, $defaultHandler)
			;
		}
		else
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
