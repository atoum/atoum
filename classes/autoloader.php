<?php

namespace mageekguy\atoum;

require_once __DIR__ . '/../constants.php';

class autoloader
{
	protected static $autoloader = null;

	protected $directories = array(__NAMESPACE__ => array(__DIR__));

	public function register($prepend = false)
	{
		if (spl_autoload_register(array($this, 'includeClass'), true, $prepend) === false)
		{
			throw new \runtimeException('Unable to register autoloader \'' . get_class($this) . '\'');
		}

		return $this;
	}

	public function unregister()
	{
		if (spl_autoload_unregister(array($this, 'getClass')) === false)
		{
			throw new \runtimeException('Unable to unregister');
		}

		return $this;
	}

	public function addDirectory($namespace, $directory)
	{
		$namespace = trim($namespace, '\\');
		$directory = rtrim($directory, DIRECTORY_SEPARATOR);

		if (isset($this->directories[$namespace]) === false || in_array($directory, $this->directories[$namespace]) === false)
		{
			$this->directories[$namespace][] = $directory;

			krsort($this->directories, \SORT_STRING);
		}

		return $this;
	}

	public function getDirectories()
	{
		return $this->directories;
	}

	public function includeClass($class)
	{
		foreach ($this->directories as $namespace => $directories)
		{
			if ($class !== $namespace)
			{
				$namespaceLength = strlen($namespace);

				if (strncmp($class, $namespace, $namespaceLength) === 0)
				{
					$classFile = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $namespaceLength)) . '.php';

					foreach ($directories as $directory)
					{
						$path = $directory . $classFile;

						@include($path);

						if (class_exists($class, false) === true)
						{
							return $path;
						}
					}
				}
			}
		}

		return null;
	}

	public static function set()
	{
		if (static::$autoloader === null)
		{
			static::$autoloader = new static();
			static::$autoloader->register();
		}

		return static::$autoloader;
	}

	public static function get()
	{
		return static::$autoloader;
	}
}

autoloader::set();

?>
