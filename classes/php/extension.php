<?php

namespace mageekguy\atoum\php;

class extension
{
	protected $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function isLoaded()
	{
		return extension_loaded($this->name);
	}

	public function requireExtension()
	{
		if ($this->isLoaded() === false)
		{
			throw new exception('PHP extension \'' . $this->name . '\' is not loaded');
		}

		return $this;
	}
} 
