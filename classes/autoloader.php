<?php

namespace mageekguy\atoum;

class autoloader
{
	protected static $autoloader = null;

	protected $directories = array();
	protected $classAliases = array();
	protected $namespaceAliases = array();

	public function __construct(array $namespaces = null, array $namespaceAliases = null, $classAliases = null)
	{
		if ($namespaces === null)
		{
			$namespaces = array(__NAMESPACE__ => __DIR__);
		}

		foreach ($namespaces as $namespace => $directory)
		{
			$this->addDirectory($namespace, $directory);
		}

		if ($namespaceAliases === null)
		{
			$namespaceAliases = array('atoum' => __NAMESPACE__);
		}

		foreach ($namespaceAliases as $alias => $target)
		{
			$this->addNamespaceAlias($alias, $target);
		}

		if ($classAliases === null)
		{
			$classAliases = array('atoum' => __NAMESPACE__ . '\test');
		}

		foreach ($classAliases as $alias => $target)
		{
			$this->addClassAlias($alias, $target);
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
		$namespace = strtolower(trim($namespace, '\\') . '\\');
		$directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

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

	public function addNamespaceAlias($alias, $target)
	{
		$this->namespaceAliases[trim($alias, '\\') . '\\'] = trim($target, '\\') . '\\';

		return $this;
	}

	public function getNamespaceAliases()
	{
		return $this->namespaceAliases;
	}

	public function addClassAlias($alias, $target)
	{
		$this->classAliases[trim($alias, '\\')] = trim($target, '\\');

		return $this;
	}

	public function getClassAliases()
	{
		return $this->classAliases;
	}

	public function getPath($class)
	{
		$caseInsentiveClass = strtolower($class);

		foreach ($this->directories as $namespace => $directories)
		{
			if ($caseInsentiveClass !== $namespace)
			{
				$namespaceLength = strlen($namespace);

				if (substr($caseInsentiveClass, 0, $namespaceLength) == $namespace)
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

		$realClass = $this->resolveNamespaceAlias($this->resolveClassAlias($class));

		if (class_exists($realClass, false) === false && interface_exists($realClass, false) === false && ($path = $this->getPath($realClass)) !== null)
		{
			require $path;
		}

		if (class_exists($realClass, false) === true || interface_exists($realClass, false) === true)
		{
			if ($realClass !== $class)
			{
				class_alias($realClass, $class);
			}
			else
			{
				$alias = $this->getClassAlias($realClass);

				if ($alias === null)
				{
					$alias = $this->getNamespaceAlias($realClass);
				}

				if ($alias !== null)
				{
					class_alias($realClass, $alias);
				}
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

	protected function resolveNamespaceAlias($class)
	{
		foreach ($this->namespaceAliases as $alias => $target)
		{
			if (strpos($class, $alias) === 0)
			{
				return $target . substr($class, strlen($alias));
			}
		}

		return $class;
	}

	protected function getNamespaceAlias($class)
	{
		foreach ($this->namespaceAliases as $alias => $target)
		{
			if (strpos($class, $target) === 0)
			{
				return $alias . substr($class, strlen($target));
			}
		}

		return null;
	}

	protected function resolveClassAlias($class)
	{
		foreach ($this->classAliases as $alias => $target)
		{
			if ($alias === $class)
			{
				return $target;
			}
		}

		return $class;
	}

	protected function getClassAlias($class)
	{
		foreach ($this->classAliases as $alias => $target)
		{
			if ($target === $class)
			{
				return $alias;
			}
		}

		return null;
	}
}

autoloader::set();
