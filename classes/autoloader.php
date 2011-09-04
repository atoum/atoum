<?php

namespace mageekguy\atoum;

require_once __DIR__ . '/../constants.php';

class autoloader
{
	protected static $autoloader = null;

	protected $directories = array(__NAMESPACE__ => array(__DIR__));

	public function __construct()
	{
		$this->addDirectory(__NAMESPACE__, directory . '/' . basename(__DIR__));
	}

	public function register($prepend = false)
	{
		if (spl_autoload_register(array($this, 'getClass'), true, $prepend) === false)
		{
			throw new \runtimeException('Unable to register');
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

	public function getPath($class)
	{
		foreach ($this->directories as $namespace => $directories)
		{
			if ($class !== $namespace && strpos($class, $namespace) === 0)
			{
				foreach ($directories as $directory)
				{
					$path = $directory . str_replace('\\', DIRECTORY_SEPARATOR, str_replace($namespace, '', $class)) . '.php';

					if (is_file($path) === true)
					{
						return $path;
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

	protected function getClass($class)
	{
		if (($path = $this->getPath($class)) !== null)
		{
			require $path;
		}
	}
}

autoloader::set();

?>
