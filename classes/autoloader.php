<?php

namespace mageekguy\atoum;

class autoloader
{
	protected static $directories = array(__NAMESPACE__ => array(__DIR__));

	public static function register()
	{
		if (spl_autoload_register(array(__CLASS__, 'getClass')) === false)
		{
			throw new \runtimeException('Unable to register ' . __NAMESPACE__ . ' autoloader');
		}
	}

	public static function addDirectory($namespace, $directory)
	{
		if (isset(self::$directories[$namespace]) === false || in_array($directory, self::$directories[$namespace]) === false)
		{
			self::$directories[$namespace][] = $directory;
		}
	}

	public static function getDirectories()
	{
		return self::$directories;
	}

	public static function getPath($class)
	{
		$path = null;

		$class = ltrim($class, '\\');

		foreach (self::$directories as $namespace => $directories)
		{
			if ($class !== $namespace && stripos($class, $namespace) === 0)
			{
				foreach ($directories as $directory)
				{
					$path = $directory . str_replace('\\', DIRECTORY_SEPARATOR, str_replace($namespace, '', $class)) . '.php';

					if ($path !== null && file_exists($path) === true)
					{
						return $path;
					}
				}
			}
		}

		return null;
	}

	protected static function getClass($class, adapter $adapter = null)
	{
		$path = self::getPath($class);

		if ($path !== null)
		{
			require($path);
		}
	}
}

autoloader::register();

?>
