<?php

namespace mageekguy\atoum\mock;

use \mageekguy\atoum\mock;
use \mageekguy\atoum\exceptions;

class controller
{
	protected $mockClass = null;
	protected $calls = array();
	protected $returnValues = array();
	protected $at = null;
	protected $atReturnValues = array();

	protected static $injectInNextInstance = null;

	private $reflectionClassInjector = null;

	public function __set($method, $mixed)
	{
		$this->checkMethod($method);

		if ($mixed instanceof \closure === false)
		{
			$mixed = function() use ($mixed) { return $mixed; };
		}

		if ($this->at === null)
		{
			$this->returnValues[$method] = $mixed;
		}
		else
		{
			$this->atReturnValues[$method][$this->at] = $mixed;
			$this->at = null;
		}
	}

	public function __get($method)
	{
		$this->checkMethod($method);

		$return = null;

		if ($this->at === null)
		{
			if (isset($this->returnValues[$method]) === true)
			{
				$return = $this->returnValues[$method];
			}
		}
		else if (isset($this->atReturnValues[$method]) === true && isset($this->atReturnValues[$method][$this->at]) === true)
		{
			$return = $this->atReturnValues[$method][$this->at];
			$this->at = null;
		}

		return $return;
	}

	public function __isset($method)
	{
		$this->checkMethod($method);

		$isset = false;

		if ($this->at === null)
		{
			$isset = (isset($this->returnValues[$method]) === true || isset($this->atReturnValues[$method]) === true);
		}
		else
		{
			$isset = (isset($this->atReturnValues[$method]) === true && isset($this->atReturnValues[$method][$this->at]) === true);
			$this->at = null;
		}

		return $isset;
	}

	public function __unset($method)
	{
		$this->checkMethod($method);

		if ($this->at === null)
		{
			if (isset($this->returnValues[$method]) === true)
			{
				$this->returnValues[$method] = null;
			}
		}
		else
		{
			if (isset($this->atReturnValues[$method]) === false || isset($this->atReturnValues[$method][$this->at]) === false)
			{
				throw new exceptions\runtime('Method \'' . $method . '\' has no return value at call ' . $this->at);
			}

			unset($this->atReturnValues[$method][$this->at]);
			$this->at = null;
		}
	}

	public function atCall($at)
	{
		$at = (int) $at;

		if ($at < 1)
		{
			throw new exceptions\logic\invalidArgument('Call number must be greater than or equal to 1');
		}

		$this->at = $at;

		return $this;
	}

	public function getMockClass()
	{
		return $this->mockClass;
	}

	public function getMethods()
	{
		return $this->returnValues;
	}

	public function getCalls($method = null)
	{
		if ($method !== null && isset($this->returnValues[$method]) === false)
		{
			throw new exceptions\logic('Method \'' . $method . '\' is not mocked');
		}

		return ($method === null ? $this->calls : (isset($this->calls[$method]) === false ? array() : $this->calls[$method]));
	}

	public function resetCalls()
	{
		$this->calls = array();

		return $this;
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

			foreach ($this->returnValues as $method => $closure)
			{
				if (in_array($method, $methods) === false)
				{
					throw new exceptions\logic('Method \'' . $this->mockClass . '::' . $method . '()\' does not exist');
				}
			}

			foreach ($methods as $method)
			{
				if (isset($this->returnValues[$method]) === false)
				{
					$this->returnValues[$method] = null;
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
		$this->returnValues = array();
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

		$callsNumber = sizeof($this->calls[$method]);

		$mixed = isset($this->atReturnValues[$method]) === false || isset($this->atReturnValues[$method][$callsNumber]) === false ? $this->returnValues[$method] : $this->atReturnValues[$method][$callsNumber];

		return $mixed instanceof \closure === false ? $mixed : call_user_func_array($mixed, $arguments);
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
			if (array_key_exists($method, $this->returnValues) === false)
			{
				throw new exceptions\logic('Method \'' . $this->mockClass . '::' . $method . '()\' does not exist');
			}
		}
	}
}

?>
