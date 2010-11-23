<?php

namespace mageekguy\atoum\mock;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions;

class generator
{
	protected $adapter = null;
	protected $shuntedMethods = array();

	private $reflectionClassInjector = null;

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
		$reflectionClass = null;

		if ($this->reflectionClassInjector === null)
		{
			$reflectionClass = new \reflectionClass($class);
		}
		else
		{
			$reflectionClass = $this->reflectionClassInjector->__invoke($class);

			if ($reflectionClass instanceof \reflectionClass === false)
			{
				throw new exceptions\runtime\unexpectedValue('Reflection class injector must return a \reflectionClass instance');
			}
		}

		return $reflectionClass;
	}

	public function setReflectionClassInjector(\closure $reflectionClassInjector)
	{
		$closure = new \reflectionMethod($reflectionClassInjector, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new exceptions\logic\argument('Reflection class injector must take one argument');
		}

		$this->reflectionClassInjector = $reflectionClassInjector;

		return $this;
	}

	public function overload(php\method $method)
	{
		$this->methods[$method->getName()] = $method;

		return $this;
	}

	public function shunt($method)
	{
		$this->shuntedMethods[] = $method;

		return $this;
	}

	public function getMockedClassCode($class, $mockNamespace = null, $mockClass = null)
	{
		$code = null;

		$class = '\\' . ltrim($class, '\\');

		if ($mockNamespace === null)
		{
			$mockNamespace = self::getNamespace($class);
		}

		if ($mockClass === null)
		{
			$mockClass = self::getClassName($class);
		}

		if ($this->adapter->class_exists($mockNamespace . '\\' . $mockClass, false) === true || $this->adapter->interface_exists($mockNamespace . '\\' . $mockClass, false) === true)
		{
			throw new exceptions\logic('Class \'' . $mockNamespace . '\\' . $mockClass . '\' already exists');
		}

		if ($this->adapter->class_exists($class, true) === false && $this->adapter->interface_exists($class, true) === false)
		{
			$code = $this->generateUnknownClass($class, $mockNamespace, $mockClass);
		}
		else
		{
			$reflectionClass = $this->getReflectionClass($class);

			if ($reflectionClass instanceof \reflectionClass === false)
			{
				throw new exceptions\logic('Reflection class injector does not return a \reflectionClass instance');
			}

			if ($reflectionClass->isFinal() === true)
			{
				throw new exceptions\logic('Class \'' . $class . '\' is final, unable to mock it');
			}

			$code = $reflectionClass->isInterface() === false ? $this->generateClassCode($reflectionClass, $mockNamespace, $mockClass) : $this->generateInterfaceCode($reflectionClass, $mockNamespace, $mockClass);
		}

		return $code;
	}

	public function generate($class, $mockNamespace = null, $mockClass = null)
	{
		eval($this->getMockedClassCode($class, $mockNamespace, $mockClass));

		return $this;
	}

	protected function generateUnknownClass($class, $mockNamespace, $mockClass)
	{
		return 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
			'final class ' . $mockClass . ' implements \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
			'{' . PHP_EOL .
			'	private $mockController = null;' . PHP_EOL .
			'	public function getMockController()' . PHP_EOL .
			'	{' . PHP_EOL .
			'		if ($this->mockController === null)' . PHP_EOL .
			'		{' . PHP_EOL .
			'			$this->setMockController(new \\' . __NAMESPACE__ . '\\controller());' . PHP_EOL .
			'		}' . PHP_EOL .
			'		return $this->mockController;' . PHP_EOL .
			'	}' . PHP_EOL .
			'	public function setMockController(\\' . __NAMESPACE__ . '\\controller $controller)' . PHP_EOL .
			'	{' . PHP_EOL .
			'		if ($this->mockController !== $controller)' . PHP_EOL .
			'		{' . PHP_EOL .
			'			$this->mockController = $controller->control($this);' . PHP_EOL .
			'		}' . PHP_EOL .
			'		return $this->mockController;' . PHP_EOL .
			'	}' . PHP_EOL .
			'	public function resetMockController()' . PHP_EOL .
			'	{' . PHP_EOL .
			'		if ($this->mockController !== null)' . PHP_EOL .
			'		{' . PHP_EOL .
			'			$mockController = $this->mockController;' . PHP_EOL .
			'			$this->mockController = null;' . PHP_EOL .
			'			$mockController->reset();' . PHP_EOL .
			'		}' . PHP_EOL .
			'		return $this;' . PHP_EOL .
			'	}' . PHP_EOL .
			'	public function __construct(\mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
			'	{' . PHP_EOL .
			'		if ($mockController === null)' . PHP_EOL .
			'		{' . PHP_EOL .
			'			$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
			'		}' . PHP_EOL .
			'		if ($mockController === null)' . PHP_EOL .
			'		{' . PHP_EOL .
			'			$mockController = new \mageekguy\atoum\mock\controller();' . PHP_EOL .
			'		}' . PHP_EOL .
			'		if ($mockController !== null)' . PHP_EOL .
			'		{' . PHP_EOL .
			'			$this->setMockController($mockController);' . PHP_EOL .
			'		}' . PHP_EOL .
			'		if ($this->mockController !== null && isset($this->mockController->__construct) === true)' . PHP_EOL .
			'		{' . PHP_EOL .
			'			$this->mockController->invoke(\'__construct\', array());' . PHP_EOL .
			'		}' . PHP_EOL .
			'	}' . PHP_EOL .
			'}' . PHP_EOL .
			'}'
		;
	}

	protected function generateClassCode(\reflectionClass $class, $mockNamespace, $mockClass)
	{
		return 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL
			. 'final class ' . $mockClass . ' extends \\' . $class->getName() . ' implements \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL
			. '{' . PHP_EOL
			. '	private $mockController = null;' . PHP_EOL
			. '	public function getMockController()' . PHP_EOL
			. '	{' . PHP_EOL
			. '		if ($this->mockController === null)' . PHP_EOL
			. '		{' . PHP_EOL
			. '			$this->setMockController(new \\' . __NAMESPACE__ . '\\controller());' . PHP_EOL
			. '		}' . PHP_EOL
			. '		return $this->mockController;' . PHP_EOL
			. '	}' . PHP_EOL
			. '	public function setMockController(\\' . __NAMESPACE__ . '\\controller $controller)' . PHP_EOL
			. '	{' . PHP_EOL
			. '		if ($this->mockController !== $controller)' . PHP_EOL
			. '		{' . PHP_EOL
			. '			$this->mockController = $controller->control($this);' . PHP_EOL
			. '		}' . PHP_EOL
			. '		return $this->mockController;' . PHP_EOL
			. '	}' . PHP_EOL
			. '	public function resetMockController()' . PHP_EOL
			. '	{' . PHP_EOL
			. '		if ($this->mockController !== null)' . PHP_EOL
			. '		{' . PHP_EOL
			. '			$mockController = $this->mockController;' . PHP_EOL
			. '			$this->mockController = null;' . PHP_EOL
			. '			$mockController->reset();' . PHP_EOL
			. '		}' . PHP_EOL
			. '		return $this;' . PHP_EOL
			. '	}'
			. $this->generateClassMethodCode($class)
			. '}' . PHP_EOL
			. '}'
		;
	}

	protected function generateClassMethodCode(\reflectionClass $class)
	{
		$mockedMethods = '';

		foreach ($class->getMethods(\reflectionMethod::IS_PUBLIC) as $method)
		{
			if ($method->isFinal() === false && $method->isStatic() === false)
			{
				$methodName = $method->getName();

				$isConstructor = false;
				$parameters = array();

				if (isset($this->methods[$methodName]) === true)
				{
					foreach ($this->methods[$methodName]->getArguments() as $argument)
					{
						$parameters[] = $argument->getVariable();
					}

					$isConstructor = $this->methods[$methodName]->isConstructor();

					if ($isConstructor === true)
					{
						$this->methods[$methodName]->addArgument(php\method\argument::get('mockController')
								->isObject('\\' . __NAMESPACE__ . '\\controller')
								->setDefaultValue(null)
							)
						;
					}

					$methodCode = "\n	" . ((string) $this->methods[$methodName]). "\n	" . '{' . PHP_EOL;

					unset($this->methods[$methodName]);
				}
				else
				{
					$methodCode = "\n	" . 'public function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $methodName;

					$isConstructor = $method->isConstructor();

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

					if ($isConstructor === true)
					{
						$parameters[] = '\\' . __NAMESPACE__ . '\\controller $mockController = null';
					}

					$methodCode .= '(' . join(', ', $parameters) . ')' . PHP_EOL;
					$methodCode .= "	" . '{' . PHP_EOL;

					$parameters = array();

					foreach ($method->getParameters() as $parameter)
					{
						$parameters[] = '$' . $parameter->getName();
					}
				}

				$parameters = (sizeof($parameters) <= 0 ? '' : join(', ', $parameters));

				$isShunted = (in_array($methodName, $this->shuntedMethods) === true);

				if ($isConstructor === true)
				{
					$methodCode .= "		" . 'if ($mockController === null)' . PHP_EOL;
					$methodCode .= "		" . '{' . PHP_EOL;
					$methodCode .= "			" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL;

					if ($isShunted === true)
					{
						$methodCode .= "			" . 'if ($mockController === null)' . PHP_EOL;
						$methodCode .= "			" . '{' . PHP_EOL;
						$methodCode .= "				" . '$mockController = new \mageekguy\atoum\mock\controller();' . PHP_EOL;
						$methodCode .= "			" . '}' . PHP_EOL;
					}

					$methodCode .= "		" . '}' . PHP_EOL;
					$methodCode .= "		" . 'if ($mockController !== null)' . PHP_EOL;
					$methodCode .= "		" . '{' . PHP_EOL;
					$methodCode .= "			" . '$this->setMockController($mockController);' . PHP_EOL;
					$methodCode .= "		" . '}' . PHP_EOL;
				}

				if ($isShunted === true)
				{
					$methodCode .= "		" . 'if (isset($this->mockController->' . $methodName . ') === false)' . PHP_EOL;
					$methodCode .= "		" . '{' . PHP_EOL;
					$methodCode .= "			" . '$this->mockController->' . $methodName . ' = function() {};' . PHP_EOL;
					$methodCode .= "		" . '}' . PHP_EOL;
					$methodCode .=	"		" . ($isConstructor === true ? '' : 'return ') . '$this->mockController->invoke(\'' . $methodName . '\', array(' . $parameters . '));' . PHP_EOL;
				}
				else
				{
					$methodCode .=
						  "		" . 'if ($this->mockController !== null && isset($this->mockController->' . $methodName . ') === true)' . PHP_EOL
						. "		" . '{' . PHP_EOL
						. "			" . ($isConstructor === true ? '' : 'return ') . '$this->mockController->invoke(\'' . $methodName . '\', array(' . $parameters . '));' . PHP_EOL
						. "		" . '}' . PHP_EOL
						. "		" . 'else' . PHP_EOL
						. "		" . '{' . PHP_EOL
						. "			" . ($isConstructor === true ? '' : 'return ') . 'parent::' . $methodName . '(' . $parameters . ');' . PHP_EOL
						. "		" . '}' . PHP_EOL
						;
				}

				$methodCode .= "	" . '}' . PHP_EOL;

				$mockedMethods .= $methodCode;
			}
		}

		return $mockedMethods;
	}

	protected function generateInterfaceCode(\reflectionClass $class, $mockNamespace, $mockClass)
	{
		return 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL
			. 'final class ' . $mockClass . ' implements \\' . $class->getName() . ', \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL
			. '{' . PHP_EOL
			. '	private $mockController = null;' . PHP_EOL
			. '	public function getMockController()' . PHP_EOL
			. '	{' . PHP_EOL
			. '		if ($this->mockController === null)' . PHP_EOL
			. '		{' . PHP_EOL
			. '			$this->setMockController(new \\' . __NAMESPACE__ . '\\controller());' . PHP_EOL
			. '		}' . PHP_EOL
			. '		return $this->mockController;' . PHP_EOL
			. '	}' . PHP_EOL
			. '	public function setMockController(\\' . __NAMESPACE__ . '\\controller $controller)' . PHP_EOL
			. '	{' . PHP_EOL
			. '		if ($this->mockController !== $controller)' . PHP_EOL
			. '		{' . PHP_EOL
			. '			$this->mockController = $controller->control($this);' . PHP_EOL
			. '		}' . PHP_EOL
			. '		return $this->mockController;' . PHP_EOL
			. '	}' . PHP_EOL
			. '	public function resetMockController()' . PHP_EOL
			. '	{' . PHP_EOL
			. '		if ($this->mockController !== null)' . PHP_EOL
			. '		{' . PHP_EOL
			. '			$mockController = $this->mockController;' . PHP_EOL
			. '			$this->mockController = null;' . PHP_EOL
			. '			$mockController->reset();' . PHP_EOL
			. '		}' . PHP_EOL
			. '		return $this;' . PHP_EOL
			. '	}' . PHP_EOL
			. $this->generateInterfaceMethodCode($class)
			. '}' . PHP_EOL
			. '}'
		;
	}

	protected function generateInterfaceMethodCode(\reflectionClass $class)
	{
		$mockedMethods = '';

		foreach ($class->getMethods(\reflectionMethod::IS_PUBLIC) as $method)
		{
			if ($method->isFinal() === false && $method->isStatic() === false)
			{
				$methodName = $method->getName();

				$methodCode = "	" . 'public function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $methodName;

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

				if ($isConstructor === true)
				{
					$parameters[] = '\\' . __NAMESPACE__ . '\\controller $mockController = null';
				}

				$methodCode .= '(' . join(', ', $parameters) . ')' . PHP_EOL;
				$methodCode .= "	" . '{' . PHP_EOL;

				$parameters = array();

				foreach ($method->getParameters() as $parameter)
				{
					$parameters[] = '$' . $parameter->getName();
				}

				if ($isConstructor === true)
				{
					$methodCode .= "		" . 'if ($mockController === null)' . PHP_EOL;
					$methodCode .= "		" . '{' . PHP_EOL;
					$methodCode .= "			" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL;
					$methodCode .= "			" . 'if ($mockController === null)' . PHP_EOL;
					$methodCode .= "			" . '{' . PHP_EOL;
					$methodCode .= "				" . '$mockController = new \mageekguy\atoum\mock\controller();' . PHP_EOL;
					$methodCode .= "			" . '}' . PHP_EOL;
					$methodCode .= "		" . '}' . PHP_EOL;
					$methodCode .= "		" . '$this->setMockController($mockController);' . PHP_EOL;
				}

				$parameters = (sizeof($parameters) <= 0 ? '' : join(', ', $parameters));

				$methodCode .= "		" . 'if (isset($this->mockController->' . $methodName . ') === false)' . PHP_EOL;
				$methodCode .= "		" . '{' . PHP_EOL;
				$methodCode .= "			" . '$this->mockController->' . $methodName . ' = function() {};' . PHP_EOL;
				$methodCode .= "		" . '}' . PHP_EOL;
				$methodCode .=	"		" . ($isConstructor === true ? '' : 'return ') . '$this->mockController->invoke(\'' . $methodName . '\', array(' . $parameters . '));' . PHP_EOL;

				$methodCode .= "	" . '}' . PHP_EOL;

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
