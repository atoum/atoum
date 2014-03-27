<?php

namespace mageekguy\atoum\test\assertion;

use
	mageekguy\atoum\test\assertion
;

class manager
{
	protected $aliaser = null;
	protected $propertyHandlers = array();
	protected $methodHandlers = array();
	protected $defaultHandler = null;

	public function __construct(assertion\aliaser $aliaser = null)
	{
		$this->setAliaser($aliaser);
	}

	public function __set($event, $handler)
	{
		return $this->setHandler($event, $handler);
	}

	public function __get($event)
	{
		return $this->invokePropertyHandler($event);
	}

	public function __call($event, array $arguments)
	{
		return $this->invokeMethodHandler($event, $arguments);
	}

	public function setAliaser(assertion\aliaser $aliaser = null)
	{
		$this->aliaser = $aliaser ?: new assertion\aliaser();

		return $this;
	}

	public function getAliaser()
	{
		return $this->aliaser;
	}

	public function setAlias($alias, $keyword)
	{
		$this->aliaser->aliasKeyword($keyword, $alias);

		return $this;
	}

	public function setMethodHandler($event, \closure $handler)
	{
		return $this->setHandlerIn($this->methodHandlers, $event, $handler);
	}

	public function setPropertyHandler($event, \closure $handler)
	{
		return $this->setHandlerIn($this->propertyHandlers, $event, $handler);
	}

	public function setHandler($event, \closure $handler)
	{
		return $this
			->setPropertyHandler($event, $handler)
			->setMethodHandler($event, $handler)
		;
	}

	public function setDefaultHandler(\closure $handler)
	{
		$this->defaultHandler = $handler;

		return $this;
	}

	public function invokePropertyHandler($event)
	{
		return $this->invokeHandlerFrom($this->propertyHandlers, $event);
	}

	public function invokeMethodHandler($event, array $arguments = array())
	{
		return $this->invokeHandlerFrom($this->methodHandlers, $event, $arguments);
	}

	private function setHandlerIn(array & $handlers, $event, \closure $handler)
	{
		$handlers[strtolower($event)] = $handler;

		return $this;
	}

	private function invokeHandlerFrom(array $handlers, $event, array $arguments = array())
	{
		$handler = null;

		$realEvent = strtolower($event);

		if (isset($handlers[$realEvent]) === false)
		{
			$realEvent = $this->aliaser->resolveAlias($event);
		}

		if (isset($handlers[$realEvent]) === true)
		{
			$handler = $handlers[$realEvent];
		}

		switch (true)
		{
			case $handler === null && $this->defaultHandler === null:
				throw new assertion\manager\exception('There is no handler defined for \'' . $event . '\'');

			case $handler !== null:
				return call_user_func_array($handler, $arguments);

			default:
				return call_user_func_array($this->defaultHandler, array($realEvent, $arguments));
		}
	}
}
