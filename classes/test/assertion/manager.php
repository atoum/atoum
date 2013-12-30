<?php

namespace mageekguy\atoum\test\assertion;

use
	mageekguy\atoum\test\assertion
;

class manager
{
	protected $propertyHandlers = array();
	protected $methodHandlers = array();
	protected $defaultHandler = null;

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

	public function setMethodHandler($event, \closure $handler)
	{
		$this->methodHandlers[$event] = $handler;

		return $this;
	}

	public function setPropertyHandler($event, \closure $handler)
	{
		$this->propertyHandlers[$event] = $handler;

		return $this;
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
		$handler = null;

		if (isset($this->propertyHandlers[$event]) === true)
		{
			$handler = $this->propertyHandlers[$event];
		}

		switch (true)
		{
			case $handler === null && $this->defaultHandler === null:
				throw new assertion\manager\exception('There is no handler defined for event \'' . $event . '\'');

			case $handler !== null:
				return call_user_func($handler);

			default:
				return call_user_func_array($this->defaultHandler, array($event, array()));
		}
	}

	public function invokeMethodHandler($event, array $arguments = array())
	{
		$handler = null;

		if (isset($this->methodHandlers[$event]) === true)
		{
			$handler = $this->methodHandlers[$event];
		}

		switch (true)
		{
			case $handler === null && $this->defaultHandler === null:
				throw new assertion\manager\exception('There is no handler defined for event \'' . $event . '\'');

			case $handler !== null:
				return call_user_func_array($handler, $arguments);

			default:
				return call_user_func_array($this->defaultHandler, array($event, $arguments));
		}
	}
}
