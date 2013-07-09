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

		if ($mockNamespace === null)
		{
			$mockNamespace = $this->getNamespace($class);
		}

		$class = '\\' . ltrim($class, '\\');

		if ($mockClass === null)
		{
			$mockClass = self::getClassName($class);
		}

		if ($this->adapter->class_exists($mockNamespace . '\\' . $mockClass, false) === true || $this->adapter->interface_exists($mockNamespace . '\\' . $mockClass, false) === true)
		{
			throw new exceptions\logic('Class \'' . $mockNamespace . '\\' . $mockClass . '\' already exists');
		}

		$code = '';

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

		return $this->unshuntParentClassCalls();
	}

	protected function generateClassMethodCode(\reflectionClass $class)
	{
		$mockedMethods = '';
		$mockedMethodNames = array();
		$className = $class->getName();

		$constructor = $class->getConstructor();

		if ($constructor === null)
		{
			$mockedMethods .= self::generateDefaultConstructor();
			$mockedMethodNames[] = '__construct';
		}
		else if ($constructor->isFinal() === false)
		{
			$constructorName = $constructor->getName();

			$overload = $this->getOverload($constructorName);

			if ($constructor->isPublic() === false)
			{
				$this->shuntParentClassCalls();

				if ($overload === null)
				{
					$this->overload(new php\method('__construct'));

					$overload = $this->getOverload('__construct');
				}
			}

			$parameters = $this->getParameters($constructor);

			if ($overload === null)
			{
				$mockedMethods .= "\t" . 'public function __construct(' . $this->getParametersSignature($constructor) . ')';
			}
			else
			{
				$overload
					->addArgument(
						php\method\argument::get('mockController')
							->isObject('\\' . __NAMESPACE__ . '\\controller')
							->setDefaultValue(null)
					)
				;

				$mockedMethods .= "\t" . $overload;
			}

			$mockedMethods .= PHP_EOL;
			$mockedMethods .= "\t" . '{' . PHP_EOL;
			$mockedMethods .= "\t\t" . '$arguments = array_merge(array(' . join(', ', $parameters) . '), array_slice(func_get_args(), ' . sizeof($parameters) . ', -1));' . PHP_EOL;
			$mockedMethods .= "\t\t" . 'if ($mockController === null)' . PHP_EOL;
			$mockedMethods .= "\t\t" . '{' . PHP_EOL;
			$mockedMethods .= "\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL;
			$mockedMethods .= "\t\t" . '}' . PHP_EOL;
			$mockedMethods .= "\t\t" . 'if ($mockController !== null)' . PHP_EOL;
			$mockedMethods .= "\t\t" . '{' . PHP_EOL;
			$mockedMethods .= "\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL;
			$mockedMethods .= "\t\t" . '}' . PHP_EOL;

			if ($constructor->isAbstract() === true || $this->isShunted('__construct') === true || $this->isShunted($className) === true)
			{
				$methodName = ($this->isShunted($className) === true ? $className : '__construct');

				$mockedMethods .= "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === false)' . PHP_EOL;
				$mockedMethods .= "\t\t" . '{' . PHP_EOL;
				$mockedMethods .= "\t\t\t" . '$this->getMockController()->' . $methodName . ' = function() {};' . PHP_EOL;
				$mockedMethods .= "\t\t" . '}' . PHP_EOL;
				$mockedMethods .= "\t\t" . '$this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;
			}
			else
			{
				$mockedMethods .= "\t\t" . 'if (isset($this->getMockController()->' . $constructorName . ') === true)' . PHP_EOL;
				$mockedMethods .= "\t\t" . '{' . PHP_EOL;
				$mockedMethods .= "\t\t\t" . '$this->getMockController()->invoke(\'' . $constructorName . '\', $arguments);' . PHP_EOL;
				$mockedMethods .= "\t\t" . '}' . PHP_EOL;
				$mockedMethods .= "\t\t" . 'else' . PHP_EOL;
				$mockedMethods .= "\t\t" . '{' . PHP_EOL;
				$mockedMethods .= "\t\t\t" . '$this->getMockController()->addCall(\'' . $constructorName . '\', $arguments);' . PHP_EOL;

				if ($this->shuntParentClassCalls === false)
				{
					$mockedMethods .= "\t\t\t" . 'call_user_func_array(\'parent::' . $constructorName . '\', $arguments);' . PHP_EOL;
				}

				$mockedMethods .= "\t\t" . '}' . PHP_EOL;
			}

			$mockedMethods .= "\t" . '}' . PHP_EOL;

			$mockedMethodNames[] = $constructorName;
		}

		foreach ($class->getMethods() as $method)
		{
			if ($this->methodIsMockable($method) === true)
			{
				$methodName = $method->getName();
				$mockedMethodNames[] = strtolower($methodName);
				$overload = $this->getOverload($methodName);
				$parameters = $this->getParameters($method);

				if ($overload !== null)
				{
					$mockedMethods .= "\t" . $overload;
				}
				else
				{
					$mockedMethods .= "\t" . ($method->isPublic() === true ? 'public' : 'protected') . ' function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $methodName . '(' . $this->getParametersSignature($method) . ')';
				}

				$mockedMethods .= PHP_EOL . "\t" . '{' . PHP_EOL;
				$mockedMethods .= "\t\t" . '$arguments = array_merge(array(' . join(', ', $parameters) . '), array_slice(func_get_args(), ' . sizeof($parameters) . '));' . PHP_EOL;

				if ($this->isShunted($methodName) === true || $method->isAbstract() === true)
				{
					$mockedMethods .= "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === false)' . PHP_EOL;
					$mockedMethods .= "\t\t" . '{' . PHP_EOL;
					$mockedMethods .= "\t\t\t" . '$this->getMockController()->' . $methodName . ' = function() {};' . PHP_EOL;
					$mockedMethods .= "\t\t" . '}' . PHP_EOL;
					$mockedMethods .=	"\t\t" . 'return $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;
				}
				else
				{
					$mockedMethods .= "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL;
					$mockedMethods .= "\t\t" . '{' . PHP_EOL;
					$mockedMethods .= "\t\t\t" . 'return $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;
					$mockedMethods .= "\t\t" . '}' . PHP_EOL;
					$mockedMethods .= "\t\t" . 'else' . PHP_EOL;
					$mockedMethods .= "\t\t" . '{' . PHP_EOL;

					if ($methodName === '__call')
					{
						$mockedMethods .= "\t\t\t" . '$this->getMockController()->addCall(current(array_slice($arguments, 0, 1)), current(array_slice($arguments, 1)));' . PHP_EOL;
					}

					$mockedMethods .= "\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', $arguments);' . PHP_EOL;

					if ($this->shuntParentClassCalls === false)
					{
						$mockedMethods .= "\t\t\t" . 'return call_user_func_array(\'parent::' . $methodName . '\', $arguments);' . PHP_EOL;
					}

					$mockedMethods .= "\t\t" . '}' . PHP_EOL;
				}

				$mockedMethods .= "\t" . '}' . PHP_EOL;
			}
		}

		return $mockedMethods . self::generateGetMockedMethod($mockedMethodNames);
	}

	protected function generateClassCode(\reflectionClass $class, $mockNamespace, $mockClass)
	{
		return 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
			'final class ' . $mockClass . ' extends \\' . $class->getName() . ' implements \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
			'{' . PHP_EOL .
			self::generateMockControllerMethods() .
			$this->generateClassMethodCode($class) .
			'}' . PHP_EOL .
			'}'
		;
	}

	protected function generateInterfaceMethodCode(\reflectionClass $class, $addIteratorAggregate)
	{
		$mockedMethods = '';
		$mockedMethodNames = array();
		$hasConstructor = false;

		$methods = $class->getMethods(\reflectionMethod::IS_PUBLIC);

		if ($addIteratorAggregate === true)
		{
			$iteratorInterface = call_user_func($this->reflectionClassFactory, 'iteratorAggregate');

			$methods = array_merge($methods, $iteratorInterface->getMethods(\reflectionMethod::IS_PUBLIC));
		}

		foreach ($methods as $method)
		{
			$methodName = $method->getName();

			$mockedMethodNames[] = strtolower($methodName);

			$parameters = $this->getParameters($method);

			switch (true)
			{
				case $method->isFinal() === false && $method->isStatic() === false:
					$isConstructor = $methodName === '__construct';

					if ($isConstructor === true)
					{
						$hasConstructor = true;
					}

					$methodCode = "\t" . 'public function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $methodName . '(' . $this->getParametersSignature($method) . ')' . PHP_EOL;
					$methodCode .= "\t" . '{' . PHP_EOL;
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

					$methodCode .= "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === false)' . PHP_EOL;
					$methodCode .= "\t\t" . '{' . PHP_EOL;
					$methodCode .= "\t\t\t" . '$this->getMockController()->' . $methodName . ' = function() {};' . PHP_EOL;
					$methodCode .= "\t\t" . '}' . PHP_EOL;
					$methodCode .= "\t\t" . ($isConstructor === true ? '' : 'return ') . '$this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;
					$methodCode .= "\t" . '}' . PHP_EOL;
					break;

				case $method->isStatic() === true:
					$methodCode = "\t" . 'public static function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $methodName . '(' . $this->getParametersSignature($method) . ')' . PHP_EOL;
					$methodCode .= "\t" . '{' . PHP_EOL;
					$methodCode .= "\t\t" . '$arguments = array_merge(array(' . join(', ', $parameters) . '), array_slice(func_get_args(), ' . sizeof($parameters) . ', -1));' . PHP_EOL;
					$methodCode .= "\t\t" . 'return call_user_func_array(array(\'parent\', \'' . $methodName . '\'), $arguments);' . PHP_EOL;
					$methodCode .= "\t" . '}' . PHP_EOL;
					break;

				default:
					$methodCode = '';
			}

			$mockedMethods .= $methodCode;
		}

		if ($hasConstructor === false)
		{
			$mockedMethods .= self::generateDefaultConstructor();
			$mockedMethodNames[] = '__construct';
		}

		$mockedMethods .= self::generateGetMockedMethod($mockedMethodNames);

		return $mockedMethods;
	}

	protected function generateInterfaceCode(\reflectionClass $class, $mockNamespace, $mockClass)
	{
		$addIteratorAggregate = (
				$class->isInstantiable() === false
			&& (
					$class->implementsInterface('traversable') === true
				&& $class->implementsInterface('iterator') === false
				&& $class->implementsInterface('iteratorAggregate') === false
			)
		);

		return 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
			'final class ' . $mockClass . ' implements \\' . ($addIteratorAggregate === false ? '' : 'iteratorAggregate, \\') . $class->getName() . ', \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
			'{' . PHP_EOL .
			self::generateMockControllerMethods() .
			$this->generateInterfaceMethodCode($class, $addIteratorAggregate) .
			'}' . PHP_EOL .
			'}'
		;
	}

	protected function getNamespace($class)
	{
		$class = ltrim($class, '\\');
		$lastAntiSlash = strrpos($class, '\\');

		return '\\' . $this->getDefaultNamespace() . ($lastAntiSlash === false ? '' : '\\' . substr($class, 0, $lastAntiSlash));
	}

	protected function getParameters(\reflectionMethod $method)
	{
		$parameters = array();

		$overload = $this->getOverload($method->getName());

		if ($overload === null)
		{
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
		}

		return $parameters;
	}

	protected function getParametersSignature(\reflectionMethod $method)
	{
		$parameters = array();

		$mustBeNull = $this->isOrphanized($method->getName());

		foreach ($method->getParameters() as $parameter)
		{
			$parameterCode = self::getParameterType($parameter) . ($parameter->isPassedByReference() == false ? '' : '& ') . '$' . $parameter->getName();

			switch (true)
			{
				case $parameter->isDefaultValueAvailable():
					$parameterCode .= ' = ' . var_export($parameter->getDefaultValue(), true);
					break;

				case $parameter->isOptional():
				case $mustBeNull:
					$parameterCode .= ' = null';
			}

			$parameters[] = $parameterCode;
		}

		if ($method->isConstructor() === true)
		{
			$parameters[] = '\\' . __NAMESPACE__ . '\\controller $mockController = null';
		}

		return join(', ', $parameters);
	}

	protected static function getClassName($class)
	{
		$class = ltrim($class, '\\');
		$lastAntiSlash = strrpos($class, '\\');

		return ($lastAntiSlash === false ? $class : substr($class, $lastAntiSlash + 1));
	}

	protected static function getParameterType(\reflectionParameter $parameter)
	{
		switch (true)
		{
			case $parameter->isArray():
				return 'array ';

			case method_exists($parameter, 'isCallable') && $parameter->isCallable():
				return 'callable ';

			case ($class = $parameter->getClass()):
				return '\\' . $class->getName() . ' ';

			default:
				return '';
		}
	}

	protected static function generateMockControllerMethods()
	{
		return
			"\t" . 'public function getMockController()' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . '$mockController = \mageekguy\atoum\mock\controller::getForMock($this);' . PHP_EOL .
			"\t\t" . 'if ($mockController === null)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$this->setMockController($mockController = new \\' . __NAMESPACE__ . '\\controller());' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t\t" . 'return $mockController;' . PHP_EOL .
			"\t" . '}' . PHP_EOL .
			"\t" . 'public function setMockController(\\' . __NAMESPACE__ . '\\controller $controller)' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'return $controller->control($this);' . PHP_EOL .
			"\t" . '}' . PHP_EOL .
			"\t" . 'public function resetMockController()' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . '\mageekguy\atoum\mock\controller::getForMock($this)->reset();' . PHP_EOL .
			"\t\t" . 'return $this;' . PHP_EOL .
			"\t" . '}' . PHP_EOL
		;
	}

	protected static function generateDefaultConstructor($disableMethodChecking = false)
	{
		$defaultConstructor =
			"\t" . 'public function __construct(\\' . __NAMESPACE__ . '\\controller $mockController = null)' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'if ($mockController === null)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t\t" . 'if ($mockController !== null)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL
		;

		if ($disableMethodChecking === true)
		{
			$defaultConstructor .= "\t\t" . '$this->getMockController()->disableMethodChecking();' . PHP_EOL;
		}

		$defaultConstructor .=
			"\t\t" . 'if (isset($this->getMockController()->__construct) === true)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t" . '}' . PHP_EOL
		;

		return $defaultConstructor;
	}

	protected static function generateGetMockedMethod(array $mockedMethodNames)
	{
		return
			"\t" . 'public static function getMockedMethods()' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'return ' . var_export($mockedMethodNames, true) . ';' . PHP_EOL .
			"\t" . '}' . PHP_EOL
		;
	}

	protected static function generateUnknownClassCode($class, $mockNamespace, $mockClass)
	{
		return 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
			'final class ' . $mockClass . ' implements \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
			'{' . PHP_EOL .
			self::generateMockControllerMethods() .
			self::generateDefaultConstructor(true) .
			"\t" . 'public function __call($methodName, $arguments)' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'if (isset($this->getMockController()->{$methodName}) === true)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . 'return $this->getMockController()->invoke($methodName, $arguments);' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t\t" . 'else' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$this->getMockController()->addCall($methodName, $arguments);' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t" . '}' . PHP_EOL .
			self::generateGetMockedMethod(array('__call')) .
			'}' . PHP_EOL .
			'}'
		;
	}

	protected static function methodNameIsReservedWord(\reflectionMethod $method)
	{
		switch ($method->getName())
		{
			case '__halt_compiler':
			case 'abstract':
			case 'and':
			case 'array':
			case 'as':
			case 'break':
			case 'callable':
			case 'case':
			case 'catch':
			case 'class':
			case 'clone':
			case 'const':
			case 'continue':
			case 'declare':
			case 'default':
			case 'die':
			case 'do':
			case 'echo':
			case 'else':
			case 'elseif':
			case 'empty':
			case 'enddeclare':
			case 'endfor':
			case 'endforeach':
			case 'endif':
			case 'endswitch':
			case 'endwhile':
			case 'eval':
			case 'exit':
			case 'extends':
			case 'final':
			case 'for':
			case 'foreach':
			case 'function':
			case 'global':
			case 'goto ':
			case 'if':
			case 'implements':
			case 'include':
			case 'include_once':
			case 'instanceof':
			case 'insteadof':
			case 'interface':
			case 'isset':
			case 'list':
			case 'namespace ':
			case 'new':
			case 'or':
			case 'print':
			case 'private':
			case 'protected':
			case 'public':
			case 'require':
			case 'require_once':
			case 'return':
			case 'static':
			case 'switch':
			case 'throw':
			case 'trait ':
			case 'try':
			case 'unset':
			case 'use':
			case 'var':
			case 'while':
			case 'xor':
				return true;

			default:
				return false;
		}
	}

	private function methodIsMockable(\reflectionMethod $method)
	{
		switch (true)
		{
			case $method->isFinal():
			case $method->isStatic():
			case $method->isConstructor():
			case $method->getName() === 'clone':
			case $method->getName() === '__destruct':
			case static::methodNameIsReservedWord($method):
				return false;

			case $method->isPrivate():
			case $method->isProtected() && $method->isAbstract() === false:
				return $this->isOverloaded($method->getName());

			default:
				return true;
		}
	}

}
