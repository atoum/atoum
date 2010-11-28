<?php

namespace mageekguy\atoum\mock;

use \mageekguy\atoum\mock;
use \mageekguy\atoum\exceptions;

class controller
{
	protected $mockClass = null;
	protected $methods = array();
	protected $calls = array();

	protected static $injectInNextInstance = null;

	private $reflectionClassInjector = null;

	public function __set($method, $closure)
	{
		$this->checkMethod($method);

		$this->methods[$method] = $closure;
	}

	public function __get($method)
	{
		$this->checkMethod($method);

		return (isset($this->methods[$method]) === false ? null : $this->methods[$method]);
	}

	public function __isset($method)
	{
		$this->checkMethod($method);

		return (isset($this->methods[$method]) === true && $this->methods[$method] !== null);
	}

	public function __unset($method)
	{
		$this->checkMethod($method);

		if (isset($this->methods[$method]) === true)
		{
			$this->methods[$method] = null;
		}
	}

	public function getMockClass()
	{
		return $this->mockClass;
	}

	public function getMethods()
	{
		return $this->methods;
	}

	public function getCalls($method = null)
	{
		if ($method !== null && isset($this->methods[$method]) === false)
		{
			throw new exceptions\logic('Method \'' . $method . '\' is not mocked');
		}

		return ($method === null ? $this->calls : (isset($this->calls[$method]) === false ? array() : $this->calls[$method]));
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

			array_walk($methods, function(& $value, $key) { $value = $value->getName(); });

			foreach ($this->methods as $method => $closure)
			{
				if (in_array($method, $methods) === false)
				{
					throw new exceptions\logic('Method \'' . $this->mockClass . '::' . $method . '()\' does not exist');
				}
			}

			foreach ($methods as $method)
			{
				if (isset($this->methods[$method]) === false)
				{
					$this->methods[$method] = null;
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
		$this->methods = array();
		$this->calls = array();

		return $this;
	}

	public function invoke($method, array $arguments = array())
	{
		$this->checkMethod($method);

		if (isset($this->{$method}) === false)
		{
			throw new exceptions\logic('Method ' . $method . '() is not under control');
		}

		$this->calls[$method][] = $arguments;

		return $this->methods[$method] instanceof \closure === false ? $this->methods[$method] : call_user_func_array($this->methods[$method], $arguments);
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
		if ($this->mockClass !== null)
		{
			if (array_key_exists($method, $this->methods) === false)
			{
				throw new exceptions\logic('Method \'' . $this->mockClass . '::' . $method . '()\' does not exist');
			}
		}
	}
}

?>
