<?php

namespace mageekguy\atoum\test;

use
	mageekguy\atoum\test\interpreter
;

class interpreter
{
	protected $handlers = array();
	protected $defaultHandler = null;

	public function __get($event)
	{
		return $this->invoke($event);
	}

	public function __call($event, array $arguments)
	{
		return $this->invoke($event, $arguments);
	}

	public function getDefaultHandler()
	{
		return $this->defaultHandler;
	}

	public function getHandlers()
	{
		return $this->handlers;
	}

	public function setHandler($event, \closure $handler)
	{
		$this->handlers[$event] = $handler;

		return $this;
	}

	public function setDefaultHandler(\closure $handler)
	{
		$this->defaultHandler = $handler;

		return $this;
	}

	public function invoke($event, array $arguments = array())
	{
		$handlerExists = isset($this->handlers[$event]);

		switch (true)
		{
			case $handlerExists === false && $this->defaultHandler === null:
				throw new interpreter\exception('There is no handler defined for event \'' . $event . '\'');

			case $handlerExists === true:
				return call_user_func_array($this->handlers[$event], $arguments);

			default:
				return call_user_func_array($this->defaultHandler, array($event, $arguments));
		}
	}
}
