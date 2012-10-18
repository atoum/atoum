<?php

namespace mageekguy\atoum;

class autoloader
{
	protected static $autoloader = null;

	protected $directories = array();
	protected $aliases = array();

	public function __construct(array $namespaces = array(__NAMESPACE__ => __DIR__), array $aliases = array('atoum' => __NAMESPACE__))
	{
		foreach ($namespaces as $namespace => $directory)
		{
			$this->addDirectory($namespace, $directory);
		}

		foreach ($aliases as $alias => $target)
		{
			$this->addAlias($alias, $target);
		}
	}

	public function register($prepend = false)
	{
		if (spl_autoload_register(array($this, 'requireClass'), true, $prepend) === false)
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
		if (isset($this->directories[$namespace]) === false || in_array($directory, $this->directories[$namespace]) === false)
		{
			$this->directories[trim($namespace, '\\') . '\\'][] = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

			krsort($this->directories, \SORT_STRING);
		}

		return $this;
	}

	public function getDirectories()
	{
		return $this->directories;
	}

	public function addAlias($alias, $target)
	{
		$this->aliases[trim($alias, '\\') . '\\'] = trim($target, '\\') . '\\';

		return $this;
	}

	public function getAliases()
	{
		return $this->aliases;
	}

	public function getPath($class)
	{
		foreach ($this->directories as $namespace => $directories)
		{
			if ($class !== $namespace)
			{
				$namespaceLength = strlen($namespace);

				if (substr($class, 0, $namespaceLength) == $namespace)
				{
					$classFile = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $namespaceLength)) . '.php';

					foreach ($directories as $directory)
					{
						$path = $directory . $classFile;

						if (is_file($path) === true)
						{
							return $path;
						}
					}
				}
			}
		}

		return null;
	}

	public function requireClass($class)
	{
		$class = preg_replace_callback('/(^.|\\\.)/', function($matches) { return strtolower($matches[0]); }, $class);

		$realClass = $this->resolveAlias($class);

		if (class_exists($realClass, false) === false && ($path = $this->getPath($realClass)) !== null)
		{
			require $path;

			if ($realClass != $class)
			{
				class_alias($realClass, $class);
			}
		}
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

	protected function resolveAlias($class)
	{
		foreach ($this->aliases as $alias => $target)
		{
			if (strpos($class, $alias) === 0)
			{
				$class = $target . substr($class, strlen($alias));
			}
		}

		return $class;
	}
}

autoloader::set();
