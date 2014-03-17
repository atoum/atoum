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

		if (isset($handlers[$realEvent]) === true)
		{
			$handler = $handlers[$realEvent];
		}

		switch (true)
		{
			case $handler === null && $this->defaultHandler === null:
				throw new assertion\manager\exception('There is no handler defined for event \'' . $event . '\'');

			case $handler !== null:
				return call_user_func_array($handler, $arguments);

			default:
				return call_user_func_array($this->defaultHandler, array($realEvent, $arguments));
		}
	}
}
