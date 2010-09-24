<?php

namespace mageekguy\atoum\mock;

use \mageekguy\atoum\mock;

class controller
{
	protected $mock = null;
	protected $methods = array();

	public function __set($method, \closure $closure)
	{
		$this->checkMethod($method);

		$this->methods[$method] = $closure;
	}

	public function __get($method)
	{
		$this->checkMethod($method);

		return $this->methods[$method];
	}

	public function __isset($method)
	{
		$this->checkMethod($method);

		return ($this->methods[$method] !== null);
	}

	public function __unset($method)
	{
		$this->checkMethod($method);

		$this->methods[$method] = null;
	}

	public function control(mock\aggregator $mock)
	{
		if ($this->mock !== $mock)
		{
			$this->mock = $mock;

			$reflection = new \reflectionClass($this->mock);

			$methods = $reflection->getMethods(\reflectionMethod::IS_PUBLIC);

			array_walk($methods, function(& $value, $key) { $value = $value->getName(); });

			foreach ($this->methods as $method => $closure)
			{
				if (in_array($method, $methods) === false)
				{
					throw new \logicException('Method \'' . get_class($this->mock) . '::' . $method . '()\' does not exist');
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

	public function reset()
	{
		$this->mock = null;
		$this->methods = array();

		return $this;
	}

	public function invoke($method, array $arguments = array())
	{
		$this->checkMethod($method);

		if (isset($this->{$method}) === false)
		{
			throw new \logicException('Method \'' . get_class($this->mock) . '::' . $method . '()\' is not under control');
		}

		return call_user_func_array($this->methods[$method], $arguments);
	}

	protected function checkMethod($method)
	{
		if ($this->mock !== null)
		{
			if (sizeof($this->methods) <= 0)
			{
				throw new \logicException('Class \'' . get_class($this->mock) . '\' has no public methods');
			}

			if (array_key_exists($method, $this->methods) === false)
			{
				throw new \logicException('Method \'' . get_class($this->mock) . '::' . $method . '()\' does not exist');
			}
		}
	}
}

?>
