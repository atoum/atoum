<?php

namespace mageekguy\atoum\mock;

use \mageekguy\atoum;

class generator
{
	protected $adapter = null;

	private $reflectionClassInjecter = null;

	public function __construct(atoum\adapter $adapter = null)
	{
		if ($adapter === null)
		{
			$adapter = new atoum\adapter();
		}

		$this->adapter = $adapter;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function getReflectionClass($class)
	{
		return ($this->reflectionClassInjecter === null ? new \reflectionClass($class) : $this->reflectionClassInjecter->__invoke($class));
	}

	public function setReflectionClassInjecter(\closure $reflectionClassInjecter)
	{
		$closure = new \reflectionMethod($reflectionClassInjecter, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new \runtimeException('Reflection class injecter must take one argument');
		}

		$this->reflectionClassInjecter = $reflectionClassInjecter;

		return $this;
	}

	public function overload(php\method $method)
	{
		$this->methods[$method->getName()] = $method;

		return $this;
	}

	public function getMockedClassCode($class, $mockNamespace = null, $mockClass = null)
	{
		$class = '\\' . ltrim($class, '\\');

		if ($mockNamespace === null)
		{
			$mockNamespace = self::getNamespace($class);
		}

		if ($mockClass === null)
		{
			$mockClass = self::getClassName($class);
		}

		if ($this->adapter->class_exists($class, true) === false)
		{
			throw new \logicException('Class \'' . $class . '\' does not exist');
		}

		if ($this->adapter->class_exists($mockNamespace . '\\' . $mockClass, false) === true)
		{
			throw new \logicException('Class \'' . $mockNamespace . '\\' . $mockClass . '\' already exists');
		}

		$reflectionClass = $this->getReflectionClass($class);

		if ($reflectionClass instanceof \reflectionClass === false)
		{
			throw new \logicException('Reflection class injecter does not return a \reflectionClass instance');
		}

		if ($reflectionClass->isFinal() === true)
		{
			throw new \logicException('Class \'' . $class . '\' is final, unable to mock it');
		}

		if ($reflectionClass->isAbstract() === true)
		{
			throw new \logicException('Class \'' . $class . '\' is abstract, unable to mock it');
		}

		return $this->generateClassCode($reflectionClass, $mockNamespace, $mockClass);
	}

	public function generate($class, $mockNamespace = null, $mockClass = null)
	{
		eval($this->getMockedClassCode($class, $mockNamespace, $mockClass));

		return $this;
	}

	protected function generateClassCode(\reflectionClass $class, $mockNamespace, $mockClass)
	{
		return 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . "\n"
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
			. $this->generateMethodCode($class)
			. '}' . "\n"
			. '}'
		;
	}

	protected function generateMethodCode(\reflectionClass $class)
	{
		$mockedMethods = '';

		foreach ($class->getMethods(\reflectionMethod::IS_PUBLIC) as $method)
		{
			if ($method->isFinal() === false && $method->isStatic() === false)
			{
				$methodName = $method->getName();

				$isConstrictor = false;
				$parameters = '';

				if (isset($this->methods[$methodName]) === true)
				{
					foreach ($this->methods[$methodName]->getArguments() as $argument)
					{
						$parameters[] = $argument->getVariable();
					}

					$parameters = join(', ', $parameters);

					$isConstructor = $this->methods[$methodName]->isConstructor();

					if ($isConstructor === true)
					{
						$this->methods[$methodName]->addArgument(php\method\argument::get('mockController')
								->isObject('\\' . __NAMESPACE__ . '\\controller')
								->setDefaultValue(null)
							)
						;
					}

					$methodCode = "\n\t" . ((string) $this->methods[$methodName]). "\n\t" . '{' . "\n";

					unset($this->methods[$methodName]);
				}
				else
				{
					$methodCode = "\n\t" . 'public function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $methodName;
					$isConstructor = $method->isConstructor();

					$parameters = array();

					foreach ($method->getParameters() as $parameter)
					{
						$parameterCode = $this->getParameterType($parameter) . ($parameter->isPassedByReference() == false ? '' : '& ') . '$' . $parameter->getName();

						if ($parameter->isDefaultValueAvailable() == true)
						{
							$parameterCode .= '=' . var_export($parameter->getDefaultValue(), true);
						}
						else if ($parameter->isOptional() === true)
						{
							$parameterCode .= '=null';
						}

						$parameters[] = $parameterCode;
					}

					if ($method->isConstructor() === true)
					{
						$parameters[] = '\\' . __NAMESPACE__ . '\\controller $mockController = null';
					}

					$methodCode .= '(' . join(', ', $parameters) . ')' . "\n";
					$methodCode .= "\t" . '{' . "\n";

					$parameters = array();

					foreach ($method->getParameters() as $parameter)
					{
						$parameters[] = '$' . $parameter->getName();
					}

					if ($isConstructor === false)
					{
						$parameters = (sizeof($parameters) <= 0 ? '' : join(', ', $parameters));
					}
					else
					{
						$parameters = (sizeof($parameters) <= 0 ? '' : join(', ', array_slice($parameters, 0, -1)));
					}
				}

				if ($isConstructor === false)
				{
					$methodCode .=
						  "\t\t" . 'if ($this->mockController !== null && isset($this->mockController->' . $methodName . ') === true)' . "\n"
						. "\t\t" . '{' . "\n"
						. "\t\t\t" . 'return $this->mockController->invoke(\'' . $methodName . '\', array(' . $parameters . '));' . "\n"
						. "\t\t" . '}' . "\n"
						. "\t\t" . 'else' . "\n"
						. "\t\t" . '{' . "\n"
						. "\t\t\t" . 'return parent::' . $methodName . '(' . $parameters . ');' . "\n"
						. "\t\t" . '}' . "\n"
						. "\t" . '}' . "\n"
					;
				}
				else
				{
					$methodCode .= "\t\t" . 'if ($mockController === null)' . "\n";
					$methodCode .= "\t\t" . '{' . "\n";
					$methodCode .= "\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . "\n";
					$methodCode .= "\t\t" . '}' . "\n";
					$methodCode .= "\t\t" . 'if ($mockController !== null)' . "\n";
					$methodCode .= "\t\t" . '{' . "\n";
					$methodCode .= "\t\t\t" . '$this->setMockController($mockController);' . "\n";
					$methodCode .= "\t\t" . '}' . "\n";
					$methodCode .=
						  "\t\t" . 'if ($this->mockController !== null && isset($this->mockController->' . $methodName . ') === true)' . "\n"
						. "\t\t" . '{' . "\n"
						. "\t\t\t" . '$this->mockController->invoke(\'' . $methodName . '\', array(' . $parameters . '));' . "\n"
						. "\t\t" . '}' . "\n"
						. "\t\t" . 'else' . "\n"
						. "\t\t" . '{' . "\n"
						. "\t\t\t" . 'parent::' . $methodName . '(' . $parameters . ');' . "\n"
						. "\t\t" . '}' . "\n"
						. "\t" . '}' . "\n"
					;
				}

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

	protected static function getNamespace($class)
	{
		$class = ltrim($class, '\\');

		$lastAntiSlash = strrpos($class, '\\');

		return '\\' . __NAMESPACE__ . ($lastAntiSlash === false ? '' : '\\' . substr($class, 0, $lastAntiSlash));
	}

	protected static function getClassName($class)
	{
		$class = ltrim($class, '\\');

		$lastAntiSlash = strrpos($class, '\\');

		return ($lastAntiSlash === false ? $class : substr($class, $lastAntiSlash + 1));
	}
}

?>
