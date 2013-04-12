<?php

namespace mageekguy\atoum\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\exceptions
;

class generator
{
	const defaultNamespace = 'mock';

	protected $adapter = null;
	protected $phpMethodFactory = null;
	protected $reflectionClassFactory = null;
	protected $shuntedMethods = array();
	protected $overloadedMethods = array();
	protected $orphanizedMethods = array();
	protected $shuntParentClassCalls = false;

	private $defaultNamespace = null;

	public function __construct()
	{
		$this
			->setAdapter()
			->setPhpMethodFactory()
			->setReflectionClassFactory()
		;
	}

	public function callsToParentClassAreShunted()
	{
		return $this->shuntParentClassCalls;
	}

	public function shuntParentClassCalls()
	{
		$this->shuntParentClassCalls = true;

		return $this;
	}

	public function unshuntParentClassCalls()
	{
		$this->shuntParentClassCalls = false;

		return $this;
	}

	public function setAdapter(atoum\adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new atoum\adapter();

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setPhpMethodFactory(\closure $factory = null)
	{
		$this->phpMethodFactory = $factory ?: function($method) { return new mock\php\method($method); };

		return $this;
	}

	public function getPhpMethodFactory()
	{
		return $this->phpMethodFactory;
	}

	public function setReflectionClassFactory(\closure $factory = null)
	{
		$this->reflectionClassFactory = $factory ?: function($class) { return new \reflectionClass($class); };

		return $this;
	}

	public function getReflectionClassFactory()
	{
		return $this->reflectionClassFactory;
	}

	public function setDefaultNamespace($namespace)
	{
		$this->defaultNamespace = '\\' . trim($namespace, '\\');

		return $this;
	}

	public function getDefaultNamespace()
	{
		return ($this->defaultNamespace === null ? self::defaultNamespace : $this->defaultNamespace);
	}

	public function overload(php\method $method)
	{
		$this->overloadedMethods[strtolower($method->getName())] = $method;

		return $this;
	}

	public function isOverloaded($method)
	{
		return ($this->getOverload($method) !== null);
	}

	public function getOverload($method)
	{
		return (isset($this->overloadedMethods[$method = strtolower($method)]) === false ? null : $this->overloadedMethods[$method]);
	}

	public function shunt($method)
	{
		if ($this->isShunted($method) === false)
		{
			$this->shuntedMethods[] = strtolower($method);
		}

		return $this;
	}

	public function isShunted($method)
	{
		return (in_array(strtolower($method), $this->shuntedMethods) === true);
	}

	public function orphanize($method)
	{
		if ($this->isOrphanized($method) === false)
		{
			$this->orphanizedMethods[] = strtolower($method);
		}

		return $this->shunt($method);
	}

	public function isOrphanized($method)
	{
		return (in_array($method, $this->orphanizedMethods) === true);
	}

	public function getMockedClassCode($class, $mockNamespace = null, $mockClass = null)
	{
		if (trim($class, '\\') == '' || rtrim($class, '\\') != $class)
		{
			throw new exceptions\runtime('Class name \'' . $class . '\' is invalid');
		}

		$code = '';

		$class = '\\' . ltrim($class, '\\');

		if ($mockNamespace === null)
		{
			$mockNamespace = $this->getNamespace($class);
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
			$code = self::generateUnknownClassCode($class, $mockNamespace, $mockClass);
		}
		else
		{
			$reflectionClass = call_user_func($this->reflectionClassFactory, $class);

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

		$this->shuntedMethods = $this->overloadedMethods = $this->orphanizedMethods = array();

		return $this;
	}

	protected function getNamespace($class)
	{
		$class = ltrim($class, '\\');

		$lastAntiSlash = strrpos($class, '\\');

		return '\\' . $this->getDefaultNamespace() . ($lastAntiSlash === false ? '' : '\\' . substr($class, 0, $lastAntiSlash));
	}

	protected function generateClassMethodCode(\reflectionClass $class)
	{
		$mockedMethods = '';

		$className = $class->getName();

		$mockedMethodNames = array();

		$constructor = $class->getConstructor();

		$hasConstructor = ($constructor !== null);

		if ($hasConstructor === true)
		{
			if ($constructor->isPublic() === false)
			{
				$this->shuntParentClassCalls();
				$this->overload(new php\method('__construct'));
			}
		}

		$methods = $class->getMethods();

		foreach ($methods as $method)
		{
			$isConstructor = $method->isConstructor() || $method->getName() === '__construct';

			if ($method->isFinal() === false && $method->isStatic() === false)
			{
				$methodCode = '';

				$methodName = $method->getName();

				switch (true)
				{
					case $method->isProtected() && $method->isAbstract():
						$parameters = array();

						foreach ($method->getParameters() as $parameter)
						{
							$parameters[] = ($parameter->isPassedByReference() === false ? '' : '& ') . '$' . $parameter->getName();
						}

						$methodCode = "\t" . 'protected function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $methodName . '(' . $this->getParameters($method) . ')' . PHP_EOL;
						$methodCode .= "\t" . '{' . PHP_EOL;
						$methodCode .= "\t\t" . '$arguments = array_merge(array(' . join(', ', $parameters) . '), array_slice(func_get_args(), ' . sizeof($parameters) . ($isConstructor === false ? '' : ', -1') . '));' . PHP_EOL;
						$methodCode .= "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === false)' . PHP_EOL;
						$methodCode .= "\t\t" . '{' . PHP_EOL;
						$methodCode .= "\t\t\t" . '$this->mockController->' . $methodName . ' = function() {};' . PHP_EOL;
						$methodCode .= "\t\t" . '}' . PHP_EOL;
						$methodCode .=	"\t\t" . ($isConstructor === true ? '' : 'return ') . '$this->mockController->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;
						$methodCode .= "\t" . '}' . PHP_EOL;

						$mockedMethodNames[] = strtolower($methodName);
						break;

					case $method->isPublic():
					case $this->isOverloaded($methodName):
						$parameters = array();

						$overload = $this->getOverload($methodName);

						if ($overload === null)
						{
							$methodCode = "\t" . 'public function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . ($isConstructor ? '__construct' : $methodName) . '(' . $this->getParameters($method, $isConstructor) . ')' . PHP_EOL;
							$methodCode .= "\t" . '{' . PHP_EOL;

							foreach ($method->getParameters() as $parameter)
							{
								$parameters[] = ($parameter->isPassedByReference() === false ? '' : '& ') . '$' . $parameter->getName();
							}
						}
						else
						{
							foreach ($overload->getArguments() as $argument)
							{
								$parameters[] = $argument->getVariable();
							}

							if ($isConstructor === true)
							{
								$overload->addArgument(php\method\argument::get('mockController')
										->isObject('\\' . __NAMESPACE__ . '\\controller')
										->setDefaultValue(null)
									)
								;
							}

							$methodCode = "\t" . $overload . PHP_EOL . "\t" . '{' . PHP_EOL;
						}

						$methodCode .= "\t\t" . '$arguments = array_merge(array(' . join(', ', $parameters) . '), array_slice(func_get_args(), ' . sizeof($parameters) . ($isConstructor === false ? '' : ', -1') . '));' . PHP_EOL;

						if ($isConstructor === true)
						{
							$methodCode .= "\t\t" . 'if ($mockController === null)' . PHP_EOL;
							$methodCode .= "\t\t" . '{' . PHP_EOL;
							$methodCode .= "\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL;
							$methodCode .= "\t\t" . '}' . PHP_EOL;
							$methodCode .= "\t\t" . 'if ($mockController !== null)' . PHP_EOL;
							$methodCode .= "\t\t" . '{' . PHP_EOL;
							$methodCode .= "\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL;
							$methodCode .= "\t\t" . '}' . PHP_EOL;
						}

						if ($this->isShunted($methodName) === true || $method->isAbstract() === true)
						{
							$methodCode .= "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === false)' . PHP_EOL;
							$methodCode .= "\t\t" . '{' . PHP_EOL;
							$methodCode .= "\t\t\t" . '$this->mockController->' . $methodName . ' = function() {};' . PHP_EOL;
							$methodCode .= "\t\t" . '}' . PHP_EOL;
							$methodCode .=	"\t\t" . ($isConstructor === true ? '' : 'return ') . '$this->mockController->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;
						}
						else if ($isConstructor === true && $methodName === $className && $this->isShunted('__construct') === true)
						{
							$methodCode .= "\t\t" . 'if (isset($this->getMockController()->__construct) === false)' . PHP_EOL;
							$methodCode .= "\t\t" . '{' . PHP_EOL;
							$methodCode .= "\t\t\t" . '$this->mockController->__construct = function() {};' . PHP_EOL;
							$methodCode .= "\t\t" . '}' . PHP_EOL;
							$methodCode .=	"\t\t" . '$this->mockController->invoke(\'__construct\', $arguments);' . PHP_EOL;
						}
						else
						{
							$methodCode .= "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL;
							$methodCode .= "\t\t" . '{' . PHP_EOL;
							$methodCode .= "\t\t\t" . ($isConstructor === true ? '' : 'return ') . '$this->mockController->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;
							$methodCode .= "\t\t" . '}' . PHP_EOL;

							if ($isConstructor === true && $methodName === $className)
							{
								$methodCode .= "\t\t" . 'else if (isset($this->getMockController()->__construct) === true)' . PHP_EOL;
								$methodCode .= "\t\t" . '{' . PHP_EOL;
								$methodCode .= "\t\t\t" . '$this->mockController->invoke(\'__construct\', $arguments);' . PHP_EOL;
								$methodCode .= "\t\t" . '}' . PHP_EOL;
							}

							$methodCode .= "\t\t" . 'else' . PHP_EOL;
							$methodCode .= "\t\t" . '{' . PHP_EOL;

							if ($methodName === '__call')
							{
								$methodCode .= "\t\t\t" . '$this->getMockController()->addCall(current(array_slice($arguments, 0, 1)), current(array_slice($arguments, 1)));' . PHP_EOL;
							}

							$methodCode .= "\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', $arguments);' . PHP_EOL;

							if ($this->shuntParentClassCalls === false)
							{
								$methodCode .= "\t\t\t" . ($isConstructor === true ? '' : 'return ') . 'call_user_func_array(\'parent::' . $methodName . '\', $arguments);' . PHP_EOL;
							}

							$methodCode .= "\t\t" . '}' . PHP_EOL;
						}

						$methodCode .= "\t" . '}' . PHP_EOL;

						$mockedMethodNames[] = strtolower($methodName);
				}

				$mockedMethods .= $methodCode;
			}
		}

		if ($hasConstructor === false)
		{
			$mockedMethods .= self::generateDefaultConstructor();
			$mockedMethodNames[] = '__construct';
		}

		$mockedMethods .= "\t" . 'public static function getMockedMethods()' . PHP_EOL;
		$mockedMethods .= "\t" . '{' . PHP_EOL;
		$mockedMethods .=	"\t\t" . 'return ' . var_export($mockedMethodNames, true) . ';' . PHP_EOL;
		$mockedMethods .= "\t" . '}' . PHP_EOL;

		return $mockedMethods;
	}

	protected function generateClassCode(\reflectionClass $class, $mockNamespace, $mockClass)
	{
		return 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
			'final class ' . $mockClass . ' extends \\' . $class->getName() . ' implements \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
			'{' . PHP_EOL .
			self::generateMockControllerMethod() .
			$this->generateClassMethodCode($class) .
			'}' . PHP_EOL .
			'}'
		;
	}

	protected static function getClassName($class)
	{
		$class = ltrim($class, '\\');

		$lastAntiSlash = strrpos($class, '\\');

		return ($lastAntiSlash === false ? $class : substr($class, $lastAntiSlash + 1));
	}

	protected static function getParameterType(\reflectionParameter $parameter)
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

	protected function getParameters(\reflectionMethod $method, $addMockController = false)
	{
		$parameters = array();

		$mustBeNull = $this->isOrphanized($method->getName());

		foreach ($method->getParameters() as $parameter)
		{
			$parameterCode = self::getParameterType($parameter) . ($parameter->isPassedByReference() == false ? '' : '& ') . '$' . $parameter->getName();

			switch (true)
			{
				case $parameter->isDefaultValueAvailable():
					$parameterCode .= '=' . var_export($parameter->getDefaultValue(), true);
					break;

				case $parameter->isOptional():
				case $mustBeNull:
					$parameterCode .= '=null';
			}

			$parameters[] = $parameterCode;
		}

		if ($addMockController === true)
		{
			$parameters[] = '\\' . __NAMESPACE__ . '\\controller $mockController = null';
		}

		return join(', ', $parameters);
	}

	protected function generateInterfaceMethodCode(\reflectionClass $class, $addIteratorInterface)
	{
		$mockedMethods = '';

		$mockedMethodNames = array();

		$hasConstructor = false;

		$methods = $class->getMethods(\reflectionMethod::IS_PUBLIC);

		if ($addIteratorInterface === true)
		{
			$iteratorInterface = call_user_func($this->reflectionClassFactory, 'iterator');

			$methods = array_merge($methods, $iteratorInterface->getMethods(\reflectionMethod::IS_PUBLIC));
		}

		foreach ($methods as $method)
		{
			if ($method->isFinal() === false && $method->isStatic() === false)
			{
				$methodName = $method->getName();

				$mockedMethodNames[] = strtolower($methodName);

				$isConstructor = $methodName === '__construct';

				if ($isConstructor === true)
				{
					$hasConstructor = true;
				}

				$parameters = array();

				foreach ($method->getParameters() as $parameter)
				{
					$parameters[] = '$' . $parameter->getName();
				}

				$parameters = join(', ', $parameters);
				$mockControllerParameters = ($parameters == '' ? 'func_get_args()' : 'array(' . $parameters . ')');

				$methodCode = "\t" . 'public function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $methodName . '(' . $this->getParameters($method, $isConstructor) . ')' . PHP_EOL;
				$methodCode .= "\t" . '{' . PHP_EOL;

				if ($isConstructor === true)
				{
					$methodCode .= "\t\t" . 'if ($mockController === null)' . PHP_EOL;
					$methodCode .= "\t\t" . '{' . PHP_EOL;
					$methodCode .= "\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL;
					$methodCode .= "\t\t" . '}' . PHP_EOL;
					$methodCode .= "\t\t" . 'if ($mockController !== null)' . PHP_EOL;
					$methodCode .= "\t\t" . '{' . PHP_EOL;
					$methodCode .= "\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL;
					$methodCode .= "\t\t" . '}' . PHP_EOL;
				}

				$methodCode .= "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === false)' . PHP_EOL;
				$methodCode .= "\t\t" . '{' . PHP_EOL;
				$methodCode .= "\t\t\t" . '$this->mockController->' . $methodName . ' = function() {};' . PHP_EOL;
				$methodCode .= "\t\t" . '}' . PHP_EOL;
				$methodCode .=	"\t\t" . ($isConstructor === true ? '' : 'return ') . '$this->mockController->invoke(\'' . $methodName . '\', ' . $mockControllerParameters . ');' . PHP_EOL;
				$methodCode .= "\t" . '}' . PHP_EOL;

				$mockedMethods .= $methodCode;
			}
		}

		if ($hasConstructor === false)
		{
			$mockedMethods .= self::generateDefaultConstructor();
			$mockedMethodNames[] = '__construct';
		}

		$mockedMethods .= "\t" . 'public static function getMockedMethods()' . PHP_EOL;
		$mockedMethods .= "\t" . '{' . PHP_EOL;
		$mockedMethods .=	"\t\t" . 'return ' . var_export($mockedMethodNames, true) . ';' . PHP_EOL;
		$mockedMethods .= "\t" . '}' . PHP_EOL;

		return $mockedMethods;
	}

	protected function generateInterfaceCode(\reflectionClass $class, $mockNamespace, $mockClass)
	{
		$addIteratorInterface = ($class->isInstantiable() === false && ($class->implementsInterface('traversable') === true && $class->implementsInterface('iterator') === false && $class->implementsInterface('iteratorAggregate') === false));

		return 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
			'final class ' . $mockClass . ' implements \\' . ($addIteratorInterface === false ? '' : 'iterator, \\') . $class->getName() . ', \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
			'{' . PHP_EOL .
			self::generateMockControllerMethod() .
			$this->generateInterfaceMethodCode($class, $addIteratorInterface) .
			'}' . PHP_EOL .
			'}'
		;
	}

	protected static function generateMockControllerMethod()
	{
		return
			"\t" . 'private $mockController = null;' . PHP_EOL .
			"\t" . 'public function getMockController()' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'if ($this->mockController === null)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$this->setMockController(new \\' . __NAMESPACE__ . '\\controller());' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t\t" . 'return $this->mockController;' . PHP_EOL .
			"\t" . '}' . PHP_EOL .
			"\t" . 'public function setMockController(\\' . __NAMESPACE__ . '\\controller $controller)' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'if ($this->mockController !== $controller)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
			"\t\t\t" . '$controller->control($this);' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t\t" . 'return $this->mockController;' . PHP_EOL .
			"\t" . '}' . PHP_EOL .
			"\t" . 'public function resetMockController()' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'if ($this->mockController !== null)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$mockController = $this->mockController;' . PHP_EOL .
			"\t\t\t" . '$this->mockController = null;' . PHP_EOL .
			"\t\t\t" . '$mockController->reset();' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t\t" . 'return $this;' . PHP_EOL .
			"\t" . '}' . PHP_EOL
		;
	}

	protected static function generateUnknownClassCode($class, $mockNamespace, $mockClass)
	{
		return 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
			'final class ' . $mockClass . ' implements \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
			'{' . PHP_EOL .
			self::generateMockControllerMethod() .
			"\t" . 'public function __construct(\mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'if ($mockController === null)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t\t" . 'if ($mockController !== null)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t\t" . '$this->getMockController()->disableMethodChecking();' . PHP_EOL .
			"\t\t" . 'if (isset($this->getMockController()->__construct) === true)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$this->mockController->invoke(\'__construct\', array());' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t" . '}' . PHP_EOL .
			"\t" . 'public function __call($methodName, $arguments)' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'if (isset($this->getMockController()->{$methodName}) === true)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . 'return $this->mockController->invoke($methodName, $arguments);' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t\t" . 'else' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$this->getMockController()->addCall($methodName, $arguments);' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t" . '}' . PHP_EOL .
			"\t" . 'public static function getMockedMethods()' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'return ' . var_export(array('__call'), true) . ';' . PHP_EOL .
			"\t" . '}' . PHP_EOL .
			'}' . PHP_EOL .
			'}'
		;
	}

	protected static function generateDefaultConstructor()
	{
		return
			  "\t" . 'public function __construct(\\' . __NAMESPACE__ . '\\controller $mockController = null)' . PHP_EOL
			. "\t" . '{' . PHP_EOL
			. "\t\t" . 'if ($mockController === null)' . PHP_EOL
			. "\t\t" . '{' . PHP_EOL
			. "\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL
			. "\t\t" . '}' . PHP_EOL
			. "\t\t" . 'if ($mockController !== null)' . PHP_EOL
			. "\t\t" . '{' . PHP_EOL
			. "\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL
			. "\t\t" . '}' . PHP_EOL
			. "\t\t" . 'if (isset($this->getMockController()->__construct) === true)' . PHP_EOL
			. "\t\t" . '{' . PHP_EOL
			. "\t\t\t" . '$this->mockController->invoke(\'__construct\', func_get_args());' . PHP_EOL
			. "\t\t" . '}' . PHP_EOL
			. "\t" . '}' . PHP_EOL
		;
	}
}
