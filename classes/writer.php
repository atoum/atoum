<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\writer\decorator
;

abstract class writer
{
	protected $adapter = null;
	protected $decorators = array();

	public function __construct(adapter $adapter = null)
	{
		$this->setAdapter($adapter);
	}

	public function setAdapter(adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new adapter();

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function reset()
	{
		return $this;
	}

	public function addDecorator(decorator $decorator)
	{
		$this->decorators[] = $decorator;

		return $this;
	}

	public function getDecorators()
	{
		return $this->decorators;
	}

	public function removeDecorators()
	{
		$this->decorators = array();

		return $this;
	}

	public function write($string)
	{
		foreach ($this->decorators as $decorator)
		{
			$string = $decorator->decorate($string);
		}

		$this->doWrite($string);

		return $this;
	}

	public abstract function clear();

	protected abstract function doWrite($string);
}
