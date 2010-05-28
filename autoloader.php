<?php

namespace mageekguy\tests\unit;

class autoloader
{
	public static function register()
	{
		if (spl_autoload_register(array(__CLASS__, 'requireClass')) === false)
		{
			throw new \runtimeException('Unable to register ' . __NAMESPACE__ . ' autoloader');
		}
	}

	protected static function requireClass($class)
	{
		$class = ltrim($class, '\\');

		if (stripos($class, __NAMESPACE__) === 0)
		{
			$class = explode('\\', $class);

			$directory = array_slice($class, 3, -1);

			$path = __DIR__ . DIRECTORY_SEPARATOR . (sizeof($directory) <= 0 ? '' : join(DIRECTORY_SEPARATOR, $directory) . DIRECTORY_SEPARATOR) . end($class) . '.php';

			if (file_exists($path) === true)
			{
				require($path);
			}
		}
	}
}

autoloader::register();

?>
