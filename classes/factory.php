<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\factory
;

class factory
{
	protected $builders = array();
	protected $currentClass = null;
	protected $importations = array();
	protected $importationsByClass = array();

	private static $classes = array();

	public function setCurrentClass($class)
	{
		$this->currentClass = trim($class, '\\');

		return $this;
	}

	public function unsetCurrentClass()
	{
		$this->currentClass = null;

		return $this;
	}

	public function getCurrentClass()
	{
		return $this->currentClass;
	}

	public function build($class, array $arguments = null)
	{
		$instance = null;

		if ($this->builderIsSet($class = $this->resolveClassName($class)) === true)
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

	public function setBuilder($class, \closure $builder)
	{
		$this->builders[$this->resolveClassName($class)] = $builder;

		return $this;
	}

	public function returnWhenBuild($class, $value)
	{
		return $this->setBuilder($class, function() use ($value) { return $value; });
	}

	public function builderIsSet($class)
	{
		return (isset($this->builders[$this->resolveClassName($class)]) === true);
	}

	public function getBuilder($class)
	{
		return ($this->builderIsSet($class) === false ? null : $this->builders[$class]);
	}

	public function getBuilders()
	{
		return $this->builders;
	}

	public function import($string, $alias = null)
	{
		$string = trim($string, '\\');

		if ($alias !== null)
		{
			$alias = trim($alias, '\\');
		}
		else if (($lastOccurence = strrpos($string, '\\')) === false)
		{
			$alias = $string;
		}
		else
		{
			$alias = substr($string, $lastOccurence + 1);
		}

		if (isset($this->importations[$alias]) === true && $this->importations[$alias] != $string)
		{
			throw new factory\exception('Unable to use \'' . $string . '\' as \'' . $alias . '\' because the name is already in use');
		}

		if ($this->currentClass === null)
		{
			$this->importations[$alias] = $string;
		}
		else
		{
			$this->importationsByClass[$this->currentClass][$alias] = $string;
		}

		return $this;
	}

	public function getImportations()
	{
		return ($this->currentClass === null ? $this->importations : (isset($this->importationsByClass[$this->currentClass]) === false ? array() : $this->importationsByClass[$this->currentClass]));
	}

	protected function resolveClassName($class)
	{
		if (($firstOccurrence = strpos($class, '\\')) === false || $firstOccurrence > 0)
		{
			if ($firstOccurrence === false)
			{
				$topLevelNamespace = $class;
			}
			else
			{
				$topLevelNamespace = substr($class, 0, $firstOccurrence);
			}

			if ($this->currentClass !== null && isset($this->importationsByClass[$this->currentClass][$topLevelNamespace]) === true)
			{
				if ($firstOccurrence === false)
				{
					$class = $this->importationsByClass[$this->currentClass][$topLevelNamespace];
				}
				else
				{
					$class = $this->importationsByClass[$this->currentClass][$topLevelNamespace] . '\\' . substr($class, $firstOccurrence + 1);
				}
			}
			else if (isset($this->importations[$topLevelNamespace]) === true)
			{
				if ($firstOccurrence === false)
				{
					$class = $this->importations[$topLevelNamespace];
				}
				else
				{
					$class = $this->importations[$topLevelNamespace] . '\\' . substr($class, $firstOccurrence + 1);
				}
			}
		}

		return $class;
	}
}

?>
