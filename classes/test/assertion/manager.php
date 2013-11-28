<?php

namespace mageekguy\atoum\test\assertion;

use
	mageekguy\atoum\test\assertion
;

class manager
{
	const propertyHandler = 1;
	const methodHandler = 2;
	const propertyAndMethodHandler = null;

	protected $handlers = array();
	protected $defaultHandler = null;

	public function __get($event)
	{
		return $this->invoke($event, array(), self::propertyHandler);
	}

	public function __set($event, $handler)
	{
		return $this->setHandler($event, $handler);
	}

	public function __call($event, array $arguments)
	{
		return $this->invoke($event, $arguments, self::methodHandler);
	}

	public function getHandlers()
	{
		return $this->handlers;
	}

	public function setHandler($event, \closure $handler)
	{
		$this->handlers[$event] = array($handler, self::propertyAndMethodHandler);

		return $this;
	}

	public function setMethodHandler($event, \closure $handler)
	{
		$this->handlers[$event] = array($handler, self::methodHandler);

		return $this;
	}

	public function setPropertyHandler($event, \closure $handler)
	{
		$this->handlers[$event] = array($handler, self::propertyHandler);

		return $this;
	}

	public function setDefaultHandler(\closure $handler)
	{
		$this->defaultHandler = $handler;

		return $this;
	}

	public function getDefaultHandler()
	{
		return $this->defaultHandler;
	}

	public function invoke($event, array $arguments = array(), $type = null)
	{
		$handlerExists = (isset($this->handlers[$event]) && ($this->handlers[$event][1] === $type || $this->handlers[$event][1] === self::propertyAndMethodHandler));

		switch (true)
		{
			case $handlerExists === false && $this->defaultHandler === null:
				throw new assertion\manager\exception('There is no handler defined for event \'' . $event . '\'');

			case $handlerExists:
				return call_user_func_array($this->handlers[$event][0], $arguments);

			default:
				return call_user_func_array($this->defaultHandler, array($event, $arguments));
		}
	}
}
