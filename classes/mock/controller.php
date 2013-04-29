<?php

namespace mageekguy\atoum\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions
;

class controller extends test\adapter
{
	protected $mockClass = null;
	protected $mockMethods = array();
	protected $iterator = null;

	protected static $controlNextNewMock = null;

	private $disableMethodChecking = false;

	public function __construct()
	{
		parent::__construct();

		$this->setIterator()->controlNextNewMock();
	}

	public function __set($method, $mixed)
	{
		$this->checkMethod($method);

		return parent::__set($method, $mixed);
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

		$this->invokers[strtolower($method)] = null;

		return $this;
	}

	public function setIterator(controller\iterator $iterator = null)
	{
		$this->iterator = $iterator ?: new controller\iterator();

		$this->iterator->setMockController($this);

		return $this;
	}

	public function getIterator()
	{
		return $this->iterator;
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

	public function getMethods()
	{
		return $this->mockMethods;
	}

	public function methods(\closure $filter = null)
	{
		$this->iterator->resetFilters();

		if ($filter !== null)
		{
			$this->iterator->addFilter($filter);
		}

		return $this->iterator;
	}

	public function methodsMatching($regex)
	{
		return $this->iterator->resetFilters()->addFilter(function($name) use ($regex) { return preg_match($regex, $name); });
	}

	public function getCalls($method = null, array $arguments = null, $identical = false)
	{
		if ($method !== null)
		{
			$this->checkMethod($method);
		}

		return parent::getCalls($method, $arguments, $identical);
	}

	public function control(mock\aggregator $mock)
	{
		$this->mockClass = get_class($mock);
		$this->mockMethods = $mock->getMockedMethods();

		if ($this->disableMethodChecking === false)
		{
			foreach (array_keys($this->invokers) as $method)
			{
				if (in_array($method, $this->mockMethods) === false)
				{
					if (in_array('__call', $this->mockMethods) === false)
					{
						throw new exceptions\logic('Method \'' . $this->getMockClass() . '::' . $method . '()\' does not exist');
					}

					if (isset($this->invokers['__call']) === false)
					{
						$this->invokers['__call'] = null;

						$this->set__call();
					}
				}
			}
		}

		foreach ($this->mockMethods as $method)
		{
			if (isset($this->invokers[$method]) === false)
			{
				$this->invokers[$method] = null;
			}
		}

		if ($mock->getMockController() !== $this)
		{
			$mock->setMockController($this);
		}

		return $this->notControlNextNewMock();
	}

	public function controlNextNewMock()
	{
		self::$controlNextNewMock = $this;

		return $this;
	}

	public function notControlNextNewMock()
	{
		if (self::$controlNextNewMock === $this)
		{
			self::$controlNextNewMock = null;
		}

		return $this;
	}

	public function reset()
	{
		$this->mockClass = null;
		$this->mockMethods = array();

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
		$instance = self::$controlNextNewMock;

		if ($instance !== null)
		{
			self::$controlNextNewMock = null;
		}

		return $instance;
	}

	protected function checkMethod($method)
	{
		if ($this->mockClass !== null && $this->disableMethodChecking === false && array_key_exists(strtolower($method), $this->invokers) === false)
		{
			if (array_key_exists('__call', $this->invokers) === true)
			{
				$this->set__call();
			}
			else
			{
				throw new exceptions\logic('Method \'' . $this->getMockClass() . '::' . $method . '()\' does not exist');
			}
		}

		return $this;
	}

	private function set__call()
	{
		$controller = $this;

		parent::__set('__call', function($method, $arguments) use ($controller) {
				return $controller->invoke($method, $arguments);
			}
		);

		return $this;
	}
}
