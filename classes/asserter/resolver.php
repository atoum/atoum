<?php

namespace mageekguy\atoum\asserter;

class resolver
{
	const defaultNamespace = 'mageekguy\atoum\asserters';

	protected $namespace = '';

	public function __construct($namespace = null)
	{
		$this->setNamespace($namespace);
	}

	public function setNamespace($namespace = null)
	{
		$this->namespace = ($namespace === null ? static::defaultNamespace : trim($namespace, '\\')) . '\\';

		return $this;
	}

	public function getNamespace()
	{
		return trim($this->namespace, '\\');
	}

	public function resolve($asserter)
	{
		$class = $asserter;

		if (strpos($class, '\\') === false)
		{
			$class = $this->namespace . $class;
		}

		if (class_exists($class, true) === false)
		{
			$class = null;
		}

		return $class;
	}
}
