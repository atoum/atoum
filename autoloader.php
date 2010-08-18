<?php

namespace mageekguy\atoum;

class autoloader
{
	public static function register()
	{
		if (spl_autoload_register(array(__CLASS__, 'getClass')) === false)
		{
			throw new \runtimeException('Unable to register ' . __NAMESPACE__ . ' autoloader');
		}
	}

	public static function getPath($class)
	{
		$path = null;

		$class = ltrim($class, '\\');

		if ($class !== __NAMESPACE__ && stripos($class, __NAMESPACE__) === 0)
		{
			$path = __DIR__ . str_replace('\\', DIRECTORY_SEPARATOR, str_replace(__NAMESPACE__, '', $class)) . '.php';
		}

		return $path;
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
