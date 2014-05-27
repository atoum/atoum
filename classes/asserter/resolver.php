<?php

namespace mageekguy\atoum\asserter;

class resolver
{
	const defaultBaseClass = 'mageekguy\atoum\asserter';
	const defaultNamespace = 'mageekguy\atoum\asserters';

	protected $baseClass = '';
	protected $namespaces = array();

	public function __construct($baseClass = null, $namespace = null)
	{
		$this
			->setBaseClass($baseClass ?: static::defaultBaseClass)
			->addNamespace($namespace ?: static::defaultNamespace)
		;
	}

	public function setBaseClass($baseClass)
	{
		$this->baseClass = trim($baseClass, '\\');

		return $this;
	}

	public function getBaseClass()
	{
		return $this->baseClass;
	}

	public function addNamespace($namespace)
	{
		$this->namespaces[] = trim($namespace, '\\');

		return $this;
	}

	public function getNamespaces()
	{
		return $this->namespaces;
	}

	public function resolve($asserter)
	{
		$class = null;

		if (strpos($asserter, '\\') !== false)
		{
			$class = $this->checkClass($asserter);
		}
		else foreach ($this->namespaces as $namespace)
		{
			$class = $this->checkClass($namespace . '\\' . $asserter);

			if ($class !== null)
			{
				break;
			}
		}

		return $class;
	}

	private function checkClass($class)
	{
		return (class_exists($class, true) === false || is_subclass_of($class, $this->baseClass) === false ? null : $class);
	}
}
