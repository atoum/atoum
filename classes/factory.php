<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\factory
;

class factory implements \arrayAccess, \serializable
{
	protected $builders = array();
	protected $importations = array();

	private static $classes = array();

	public function serialize()
	{
		return serialize($this->importations);
	}

	public function unserialize($string)
	{
		$this->importations = unserialize($string);

		return $this;
	}

	public function offsetGet($class)
	{
		$builder = $this->getBuilder($class);

		if ($builder === null)
		{
			$class = $this->resolveClass($class);

			if (class_exists($class, true) === false)
			{
				throw new factory\exception('Class \'' . $class . '\' does not exist');
			}

			$this->setBuilder($class, $builder = function() use ($class) {
					if (func_num_args() <= 0)
					{
						return new $class();
					}
					else
					{
						$class = new \reflectionClass($class);

						return $class->newInstanceArgs(func_get_args());
					}
				}
			);
		}

		return $builder;
	}

	public function offsetSet($class, $builder)
	{
		if ($builder instanceof \closure === false)
		{
			$builder = function() use ($builder) { return $builder; };
		}

		$this->setBuilder($class, $builder);
	}

	public function offsetUnset($class)
	{
		return $this->unsetBuilder($class);
	}

	public function offsetExists($class)
	{
		return $this->builderIsSet($class);
	}

	public function build($class, array $arguments = array())
	{
		return call_user_func_array($this[$class], $arguments);
	}

	public function setBuilder($class, $builder)
	{
		$this->builders[$this->resolveClass($class)] = $builder;

		return $this;
	}

	public function builderIsSet($class)
	{
		return ($this->getBuilder($class) !== null);
	}

	public function getBuilder($class)
	{
		$builder = null;

		if (sizeof($this->builders) > 0)
		{
			$class = $this->resolveClass($class);

			if (isset($this->builders[$class]) === true)
			{
				$builder = $this->builders[$class];
			}
		}

		return $builder;
	}

	public function unsetBuilder($class)
	{
		if (sizeof($this->builders) > 0)
		{
			$class = $this->resolveClass($class);

			if (isset($this->builders[$class]) === true)
			{
				unset($this->builders[$class]);
			}
		}

		return $this;
	}

	public function returnWhenBuild($class, $value)
	{
		return $this->setBuilder($class, function() use ($value) { return $value; });
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
		else
		{
			$this->importations[$alias] = $string;

			return $this;
		}
	}

	public function getImportations()
	{
		return $this->importations;
	}

	public function resetImportations()
	{
		$this->importations = array();

		return $this;
	}

	protected function resolveClass($class)
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


			if (isset($this->importations[$topLevelNamespace]) === true)
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
