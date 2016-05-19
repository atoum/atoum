<?php

namespace mageekguy\atoum\mock;

use
	mageekguy\atoum\mock,
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\adapter\call\decorators
;

class controller extends test\adapter
{
	protected $mockClass = null;
	protected $mockMethods = array();
	protected $iterator = null;
	protected $autoBind = true;

	protected static $linker = null;
	protected static $controlNextNewMock = null;
	protected static $autoBindForNewMock = true;

	private $disableMethodChecking = false;

	public function __construct()
	{
		parent::__construct();

		$this
			->setIterator()
			->controlNextNewMock()
		;

		if (self::$autoBindForNewMock === true)
		{
			$this->enableAutoBind();
		}
		else
		{
			$this->disableAutoBind();
		}
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

		return $this->setInvoker($method);
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

	public function getCalls(test\adapter\call $call = null, $identical = false)
	{
		if ($call !== null)
		{
			$this->checkMethod($call->getFunction());
		}

		return parent::getCalls($call, $identical);
	}

	public function control(mock\aggregator $mock)
	{
		$currentMockController = self::$linker->getController($mock);

		if ($currentMockController !== null && $currentMockController !== $this)
		{
			$currentMockController->reset();
		}

		if ($currentMockController === null || $currentMockController !== $this)
		{
			self::$linker->link($this, $mock);
		}

		$this->mockClass = get_class($mock);
		$this->mockMethods = $mock->getMockedMethods();

		foreach (array_keys($this->invokers) as $method)
		{
			$this->checkMethod($method);
		}

		foreach ($this->mockMethods as $method)
		{
			$this->{$method}->setMock($mock);

			if ($this->autoBind === true)
			{
				$this->{$method}->bindTo($mock);
			}
		}

		return $this
			->resetCalls()
			->notControlNextNewMock()
		;
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

	public function enableAutoBind()
	{
		$this->autoBind = true;

		foreach ($this->invokers as $invoker)
		{
			$invoker->bindTo($this->getMock());
		}

		return $this;
	}

	public function disableAutoBind()
	{
		$this->autoBind = false;

		return $this->reset();
	}

	public function autoBindIsEnabled()
	{
		return ($this->autoBind === true);
	}

	public function reset()
	{
		self::$linker->unlink($this);

		$this->mockClass = null;
		$this->mockMethods = array();

		return parent::reset();
	}

	public function getMock()
	{
		return self::$linker->getMock($this);
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

	public static function enableAutoBindForNewMock()
	{
		self::$autoBindForNewMock = true;
	}

	public static function disableAutoBindForNewMock()
	{
		self::$autoBindForNewMock = false;
	}

	public static function get($unset = true)
	{
		$instance = self::$controlNextNewMock;

		if ($instance !== null && $unset === true)
		{
			self::$controlNextNewMock = null;
		}

		return $instance;
	}

	public static function setLinker(controller\linker $linker = null)
	{
		self::$linker = $linker ?: new controller\linker();
	}

	public static function getForMock(aggregator $mock)
	{
		return self::$linker->getController($mock);
	}

	protected function checkMethod($method)
	{
		if ($this->mockClass !== null && $this->disableMethodChecking === false && in_array(strtolower($method), $this->mockMethods) === false)
		{
			if (in_array('__call', $this->mockMethods) === false)
			{
				throw new exceptions\logic('Method \'' . $this->getMockClass() . '::' . $method . '()\' does not exist');
			}

			if (isset($this->__call) === false)
			{
				$controller = $this;

				parent::__set('__call', function($method, $arguments) use ($controller) {
						return $controller->invoke($method, $arguments);
					}
				);
			}
		}

		return $this;
	}

	protected function buildInvoker($methodName, \closure $factory = null)
	{
		if ($factory === null)
		{
			$factory = function($methodName, $mock) { return new mock\controller\invoker($methodName, $mock); };
		}

		return $factory($methodName, $this->getMock());
	}

	protected function setInvoker($methodName, \closure $factory = null)
	{
		$invoker = parent::setInvoker($methodName, $factory);

		$mock = $this->getMock();

		if ($mock !== null)
		{
			$invoker->setMock($this->getMock());
		}

		if ($this->autoBind === true)
		{
			$invoker->bindTo($mock);
		}

		return $invoker;
	}

	protected function buildCall($function, array $arguments)
	{
		$call = parent::buildCall($function, $arguments);

		if ($this->mockClass !== null)
		{
			$call->setDecorator(new decorators\addClass($this->mockClass));
		}

		return $call;
	}
}

controller::setLinker();
