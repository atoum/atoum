<?php

namespace mageekguy\atoum\reflection;

class factory
{
	public function build(\reflectionClass $class, & $instance = null, & $numberOfDefaultArgument = 0)
	{
		$factory = null;
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

						$defaultValue = null;

						if ($parameter->isDefaultValueAvailable() === true)
						{
							$defaultValue = var_export($parameter->getDefaultValue(), true);
						}
						else if ($parameter->isOptional() === true)
						{
							$defaultValue = 'null';
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
					$factory = 'function() use (& $instance, $class) { return ($instance = $class->newInstanceArgs(func_get_args())); };';
				}
				else
				{
					$factory = 'function(' . join(', ', $closureParameters) . ') use (& $instance) { return ($instance = new ' . $class->getName() . '(' . join(', ', $constructorParameters) . ')); };';
				}

				$factory = eval('return ' . $factory);
			}
		}

		return $factory;
	}
}
