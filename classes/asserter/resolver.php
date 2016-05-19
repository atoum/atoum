<?php

namespace mageekguy\atoum\asserter;

use
	mageekguy\atoum\tools\variable\analyzer
;

class resolver
{
	const defaultBaseClass = 'mageekguy\atoum\asserter';
	const defaultNamespace = 'mageekguy\atoum\asserters';

	protected $baseClass = '';
	protected $namespaces = array();
	private $analyzer;
	private $resolved = array();

	public function __construct($baseClass = null, $namespace = null, analyzer $analyzer = null)
	{
		$this
			->setBaseClass($baseClass ?: static::defaultBaseClass)
			->addNamespace($namespace ?: static::defaultNamespace)
			->setAnalyzer($analyzer)
		;
	}

	public function setAnalyzer(analyzer $analyzer = null)
	{
		$this->analyzer = $analyzer ?: new analyzer();

		return $this;
	}

	public function getAnalyzer()
	{
		return $this->analyzer;
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
		if (isset($this->resolved[$asserter])) {
			return $this->resolved[$asserter];
		}

		if (false === $this->analyzer->isValidNamespace($asserter))
		{
			return null;
		}

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

			$class = $this->checkClass($namespace . '\\php' . ucfirst($asserter));

			if ($class !== null)
			{
				break;
			}
		}

		$this->resolved[$asserter] = $class;

		return $class;
	}

	private function checkClass($class)
	{
		return (class_exists($class, true) === false || is_subclass_of($class, $this->baseClass) === false ? null : $class);
	}
}
