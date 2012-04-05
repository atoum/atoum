<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\factory
;

class factory
{
	protected $builders = array();
	protected $importations = array();

	private static $classes = array();

	public function build($class, array $arguments = array(), $client = null)
	{
		$instance = null;

		if (($builder = $this->getBuilder($class, $client)) !== null)
		{
			$class = $this->resolveClass($class);

			if (($instance = call_user_func_array($builder, $arguments)) instanceof $class === false && is_subclass_of($instance, $class) === false)
			{
				throw new factory\exception('Unable to build an instance of class \'' . $class . '\' with current builder');
			}
		}
		else
		{
			$class = $this->resolveClass($class);

			if (class_exists($class, true) === false)
			{
				throw new factory\exception('Unable to build an instance of class \'' . $class . '\' because class does not exist');
			}
			else if (sizeof($arguments) <= 0)
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

	public function setBuilder($class, \closure $builder, $client = null)
	{
		$this->builders[self::resolveClient($client)][$this->resolveClass($class)] = $builder;

		return $this;
	}

	public function returnWhenBuild($class, $value, $client = null)
	{
		return $this->setBuilder($class, function() use ($value) { return $value; }, $client);
	}

	public function builderIsSet($class, $client = null)
	{
		return ($this->getBuilder($class, $client) !== null);
	}

	public function getBuilder($class, $client = null)
	{
		$builder = null;

		if (sizeof($this->builders) > 0)
		{
			$client = self::resolveClient($client);
			$class = $this->resolveClass($class);

			if (isset($this->builders[$client][$class]) === true)
			{
				$builder = $this->builders[$client][$class];
			}
			else if ($client !== null && isset($this->builders[null][$class]) === true)
			{
				$builder = $this->builders[null][$class];
			}
		}

		return $builder;
	}

	public function getBuilders($client = null)
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

	protected static function resolveClient($client)
	{
		return ($client === null ? null : is_object($client) === true ? get_class($client) : trim($client, '\\'));
	}
}

?>
