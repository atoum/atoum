<?php

namespace mageekguy\atoum\mock;

use \mageekguy\atoum;

class generator
{
	protected $adapter = null;

	public function __construct(atoum\adapter $adapter = null)
	{
		if ($adapter === null)
		{
			$adapter = new unit\adapter();
		}

		$this->adapter = $adapter;
	}

	public function generate($class, $mockClass = null)
	{
		$class = '\\' . ltrim($class, '\\');

		if ($mockClass === null)
		{
			$mockClass = self::getClassName($class);
		}

		if ($this->adapter->class_exists($class, true) === false)
		{
			throw new \logicException('Argument 1 of method \'' . __METHOD__ . '()\' must be a valid class name');
		}

		if ($this->adapter->class_exists(__NAMESPACE__ . '\\' . $mockClass, false) === true)
		{
			throw new \logicException('Argument 2 of method \'' . __METHOD__ . '()\' must be an undefined class name');
		}

		$reflectionClass = new \reflectionClass($class);

		if ($reflectionClass->isFinal() === true)
		{
			throw new \logicException('Argument 1 of method \'' . __METHOD__ . '()\' must not be a final class name');
		}

		$code = $this->getMockedClassCode($reflectionClass, $mockClass);
		eval($code);

		return $this;
	}

	protected function getMockedClassCode(\reflectionClass $class, $mockClass)
	{
		return 'namespace ' . __NAMESPACE__ . ' {' . "\n"
			. 'final class ' . $mockClass . ' extends \\' . $class->getName() . ' implements \\' . __NAMESPACE__ . '\\aggregator' . "\n"
			. '{' . "\n"
			. '	private $mockController = null;' . "\n"
			. '	public function getMockController()' . "\n"
			. '	{' . "\n"
			. '		if ($this->mockController === null)' . "\n"
			. '		{' . "\n"
			. '			$this->setMockController(new \\' . __NAMESPACE__ . '\\controller());' . "\n"
			. '		}' . "\n"
			. '		return $this->mockController;' . "\n"
			. '	}' . "\n"
			. '	public function setMockController(\\' . __NAMESPACE__ . '\\controller $controller)' . "\n"
			. '	{' . "\n"
			. '		if ($this->mockController !== $controller)' . "\n"
			. '		{' . "\n"
			. '			$this->mockController = $controller->control($this);' . "\n"
			. '		}' . "\n"
			. '		return $this->mockController;' . "\n"
			. '	}' . "\n"
			. '	public function resetMockController()' . "\n"
			. '	{' . "\n"
			. '		if ($this->mockController !== null)' . "\n"
			. '		{' . "\n"
			. '			$mockController = $this->mockController;' . "\n"
			. '			$this->mockController = null;' . "\n"
			. '			$mockController->reset();' . "\n"
			. '		}' . "\n"
			. '		return $this;' . "\n"
			. '	}'
			. $this->getMockedClassMethods($class)
			. '}' . "\n"
			. '}'
		;
	}

	protected function getMockedClassMethods(\reflectionClass $class)
	{
		$mockedMethods = '';

		foreach ($class->getMethods(\reflectionMethod::IS_PUBLIC) as $method)
		{
			if ($method->isFinal() === false && $method->isStatic() === false)
			{
				$methodName = $method->getName();

				$methodCode = "\n\t" . 'public function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $methodName;

				$parameters = array();

				foreach ($method->getParameters() as $parameter)
				{
					$parameterCode = $this->getParameterType($parameter) . ($parameter->isPassedByReference() == false ? '' : '& ') . '$' . $parameter->getName();

					if ($parameter->isDefaultValueAvailable() == true)
					{
						$parameterCode .= '=' . var_export($parameter->getDefaultValue(), true);
					}

					$parameters[] = $parameterCode;
				}

				if ($method->isConstructor() === true)
				{
					$parameters[] = '\\' . __NAMESPACE__ . '\\controller $mockController = null';
				}

				$methodCode .= '(' . join(', ', $parameters) . ')' . "\n"
					. "\t" . '{' . "\n"
				;

				$parameters = array();

				foreach ($method->getParameters() as $parameter)
				{
					$parameters[] = '$' . $parameter->getName();
				}

				$parameters = join(', ', $parameters);

				$mockController = '$this->mockController';

				if ($method->isConstructor() === true)
				{
					$methodCode .= "\t\t" . 'if ($mockController !== null)' . "\n";
					$methodCode .= "\t\t" . '{' . "\n";
					$methodCode .= "\t\t\t" . '$this->setMockController($mockController);' . "\n";
					$methodCode .= "\t\t" . '}' . "\n";
				}

				$methodCode .=
					  "\t\t" . 'if ($this->mockController !== null && isset($this->mockController->' . $methodName . ') === true)' . "\n"
					. "\t\t" . '{' . "\n"
					. "\t\t\t" . ($method->isConstructor() === true ? '' : 'return ') . '$this->mockController->invoke(\'' . $methodName . '\', array(' . $parameters . '));' . "\n"
					. "\t\t" . '}' . "\n"
					. "\t\t" . 'else' . "\n"
					. "\t\t" . '{' . "\n"
					. "\t\t\t" . ($method->isConstructor() === true ? '' : 'return ') . 'parent::' . $methodName . '(' . $parameters . ');' . "\n"
					. "\t\t" . '}' . "\n"
					. "\t" . '}' . "\n"
				;

				$mockedMethods .= $methodCode;
			}
		}

		return $mockedMethods;
	}

	protected function getParameterType(\reflectionParameter $parameter)
	{
		$type = '';

		if ($parameter->isArray() == true)
		{
			$type = 'array ';
		}
		else
		{
			$class = $parameter->getClass();

			if ($class !== null)
			{
				$type = '\\' . $class->getName() . ' ';
			}
		}

		return $type;
	}

	protected static function getClassName($class)
	{
		$lastAntiSlash = strrpos($class, '\\');

		if ($lastAntiSlash !== false)
		{
			$class = substr($class, $lastAntiSlash + 1);
		}

		return $class;
	}
}

?>
