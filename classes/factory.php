<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\factory
;

class factory
{
	protected $builders = array();
	protected $currentClass = null;
	protected $importedNamespaces = array();
	protected $importedNamespacesByClass = array();

	private static $classes = array();

	public function setCurrentClass($class)
	{
		$this->currentClass = $class;

		return $this;
	}

	public function getCurrentClass()
	{
		return $this->currentClass;
	}

	public function build($class, array $arguments = null)
	{
		$instance = null;

		if (($firstOccurrence = strpos($class, '\\')) === false || $firstOccurrence === 0)
		{
			$topLevelNamespace = $class;
		}
		else
		{
			$topLevelNamespace = substr($class, 0, $firstOccurrence);
		}

		if (isset($this->importedNamespaces[$topLevelNamespace]) === true)
		{
			$class = $this->importedNamespaces[$topLevelNamespace] . '\\' . substr($class, $firstOccurrence + 1);
		}

		if ($this->builderIsSet($class) === true)
		{
			if (($instance = $this->builders[$class]->__invoke($arguments)) instanceof $class === false)
			{
				throw new factory\exception('Unable to build an instance of class \'' . $class . '\' with current builder');
			}
		}
		else
		{
			if (class_exists($class, true) === false)
			{
				throw new factory\exception('Unable to build an instance of class \'' . $class . '\' because class does not exist');
			}

			if ($arguments === null)
			{
				$instance = new $class();
			}
			else
			{
				if (isset(self::$classes[$class]) === false)
				{
					self::$classes[$class] = new \reflectionClass($class);
				}

				$instance = self::$classes[$class]->newInstanceArgs($arguments);
			}
		}

		return $instance;
	}

	public function setBuilder($class, $value)
	{
		if ($value instanceof \closure === false)
		{
			$value = function() use ($value) { return $value; };
		}

		$this->builders[$class] = $value;

		return $this;
	}

	public function builderIsSet($class)
	{
		return (isset($this->builders[$class]) === true);
	}

	public function getBuilder($class)
	{
		return ($this->builderIsSet($class) === false ? null : $this->builders[$class]);
	}

	public function getBuilders()
	{
		return $this->builders;
	}

	public function importNamespace($namespace, $alias = null)
	{
		$namespace = trim($namespace, '\\');

		if ($alias !== null)
		{
			$alias = trim($alias, '\\');
		}
		else if (($lastOccurence = strrpos($namespace, '\\')) === false)
		{
			$alias = $namespace;
		}
		else
		{
			$alias = substr($namespace, $lastOccurence + 1);
		}

		if (isset($this->importedNamespaces[$alias]) === true && $this->importedNamespaces[$alias] != $namespace)
		{
			throw new factory\exception('Unable to use \'' . $namespace . '\' as \'' . $alias . '\' because the name is already in use');
		}

		if ($this->currentClass === null)
		{
			$this->importedNamespaces[$alias] = $namespace;
		}
		else
		{
			$this->importedNamespacesByClass[$this->currentClass][$alias] = $namespace;
		}

		return $this;
	}

	public function getImportedNamespaces()
	{
		return ($this->currentClass === null ? $this->importedNamespaces : (isset($this->importedNamespacesByClass[$this->currentClass]) === false ? array() : $this->importedNamespacesByClass[$this->currentClass]));
	}
}

?>
