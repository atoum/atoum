<?php

namespace mageekguy\atoum\mock;

use
	mageekguy\atoum\mock,
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions
;

class controller extends test\adapter
{
	protected $mockClass = null;

	protected static $injectInNextInstance = null;

	private $disableMethodChecking = false;
	private $reflectionClassInjector = null;

	public function __set($method, $mixed)
	{
		$this->checkMethod($method);

		parent::__set($method, $mixed);
	}

	public function __get($method)
	{
		$this->checkMethod($method);

		return parent::__get($method);
	}

	public function __isset($method)
	{
		$this->checkMethod($method);

		return parent::__isset($method);
	}

	public function __unset($method)
	{
		$this->checkMethod($method);

		parent::__unset($method);

		$this->callables[$method] = null;

		return $this;
	}

	public function disableMethodChecking()
	{
		$this->disableMethodChecking = true;

		return $this;
	}

	public function getMockClass()
	{
		return $this->mockClass;
	}

	public function getCalls($method = null, array $arguments = null)
	{
		if ($method !== null)
		{
			$this->checkMethod($method);
		}

		return parent::getCalls($method, $arguments);
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
			throw new exceptions\logic\invalidArgument('Reflection class injector must take one argument');
		}

		$this->reflectionClassInjector = $reflectionClassInjector;

		return $this;
	}

	public function control(mock\aggregator $mock)
	{
		$mockClass = get_class($mock);

		if ($this->mockClass !== $mockClass)
		{
			$this->mockClass = $mockClass;

			$class = $this->getReflectionClass($this->mockClass);

			$methods = array_filter($class->getMethods(\reflectionMethod::IS_PUBLIC), function ($value) {
					try
					{
						return ($value->getPrototype()->getName() != __NAMESPACE__ . '\aggregator');
					}
					catch (\exception $exception)
					{
						return true;
					}
				}
			);

			array_walk($methods, function(& $value) { $value = $value->getName(); });

			foreach ($this->callables as $method => $closure)
			{
				if (in_array($method, $methods) === false)
				{
					throw new exceptions\logic('Method \'' . $this->mockClass . '::' . $method . '()\' does not exist');
				}
			}

			foreach ($methods as $method)
			{
				if (isset($this->callables[$method]) === false)
				{
					$this->callables[$method] = null;
				}
			}

			$mock->setMockController($this);
		}

		return $this;
	}

	public function injectInNextMockInstance()
	{
		self::$injectInNextInstance = $this;

		return $this;
	}

	public function reset()
	{
		$this->mockClass = null;

		return parent::reset();
	}

	public function invoke($method, array $arguments = array())
	{
		$this->checkMethod($method);

		if (isset($this->{$method}) === false)
		{
			throw new exceptions\logic('Method ' . $method . '() is not under control');
		}

		return parent::invoke($method, $arguments);
	}

	public static function get()
	{
		$instance = self::$injectInNextInstance;

		if ($instance !== null)
		{
			self::$injectInNextInstance = null;
		}

		return $instance;
	}

	protected function checkMethod($method)
	{
		if ($this->mockClass !== null && $this->disableMethodChecking === false)
		{
			if (array_key_exists($method, $this->callables) === false)
			{
				throw new exceptions\logic('Method \'' . $this->mockClass . '::' . $method . '()\' does not exist');
			}
		}

		return $this;
	}
}

?>
