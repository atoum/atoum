<?php

namespace mageekguy\atoum;

class autoloader
{
	const defaultFileSuffix = '.php';
	const defaultCacheFileName = '%s.atoum.cache';

	protected $classes = array();
	protected $directories = array();
	protected $classAliases = array();
	protected $namespaceAliases = array();

	protected static $autoloader = null;

	private static $cacheFile = null;

	public function __construct(array $namespaces = array(), array $namespaceAliases = array(), $classAliases = array())
	{
		if (sizeof($namespaces) <= 0)
		{
			$namespaces = array(__NAMESPACE__ => __DIR__);
		}

		foreach ($namespaces as $namespace => $directory)
		{
			$this->addDirectory($namespace, $directory);
		}

		foreach ($namespaceAliases ?: array('atoum' => __NAMESPACE__) as $alias => $target)
		{
			$this->addNamespaceAlias($alias, $target);
		}

		foreach ($classAliases ?: array('atoum' => __NAMESPACE__ . '\test') as $alias => $target)
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

	public function addDirectory($namespace, $directory, $suffix = self::defaultFileSuffix)
	{
		$namespace = strtolower(trim($namespace, '\\') . '\\');
		$directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		if ($this->directoryIsSet($namespace, $directory) === false)
		{
			$this->directories[$namespace][] = array($directory, $suffix);

			krsort($this->directories, \SORT_STRING);
		}

		return $this;
	}

	public function directoryIsSet($namespace, $directory)
	{
		if (isset($this->directories[$namespace]) === true)
		{
			foreach ($this->directories[$namespace] as $directoryData)
			{
				if ($directoryData[0] == $directory)
				{
					return true;
				}
			}
		}

		return false;
	}

	public function getDirectories()
	{
		return $this->directories;
	}

	public function getClasses()
	{
		return $this->classes;
	}

	public function setClasses(array $classes)
	{
		$this->classes = $classes;

		return $this;
	}

	public function addNamespaceAlias($alias, $target)
	{
		$this->namespaceAliases[strtolower(trim($alias, '\\')) . '\\'] = trim($target, '\\') . '\\';

		return $this;
	}

	public function getNamespaceAliases()
	{
		return $this->namespaceAliases;
	}

	public function addClassAlias($alias, $target)
	{
		$this->classAliases[strtolower(trim($alias, '\\'))] = trim($target, '\\');

		return $this;
	}

	public function getClassAliases()
	{
		return $this->classAliases;
	}

	public function getPath($class)
	{
		$class = strtolower($class);

		$path = (isset($this->classes[$class]) === false || is_file($this->classes[$class]) === false ? null : $this->classes[$class]);

		if ($path === null && $this->handleNamespaceOfClass($class) === true)
		{
			$classes = array();

			foreach ($this->directories as $namespace => $directories)
			{
				foreach ($directories as $directoryData)
				{
					list($directory, $suffix) = $directoryData;

					$directoryLength = strlen($directory);
					$suffixLength = - strlen($suffix);

					foreach (new \recursiveIteratorIterator(new \recursiveDirectoryIterator($directory, \filesystemIterator::SKIP_DOTS|\filesystemIterator::CURRENT_AS_FILEINFO), \recursiveIteratorIterator::LEAVES_ONLY) as $file)
					{
						$filePath = $file->getPathname();

						$classes[$namespace . strtolower(str_replace('/', '\\', substr($filePath, $directoryLength, $suffixLength)))] = $filePath;
					}
				}
			}

			if ($classes != $this->classes)
			{
				$this->classes = $classes;

				$cacheFile = static::getCacheFile();

				if (@file_put_contents($cacheFile, serialize($this)) === false)
				{
					throw new \runtimeException('Unable to write in  \'' . $cacheFile . '\'');
				}

				$path = (isset($this->classes[$class]) === false ? null : $this->classes[$class]);
			}
		}

		return $path;
	}

	public function requireClass($class)
	{
		$class = strtolower($class);

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
			$cacheContents = @file_get_contents(static::getCacheFile());

			if ($cacheContents !== false)
			{
				static::$autoloader = @unserialize($cacheContents) ?: null;
			}

			if (static::$autoloader === null)
			{
				static::$autoloader = new static();
			}

			static::$autoloader->register();
		}

		return static::$autoloader;
	}

	public static function get()
	{
		return static::set();
	}

	public static function setCacheFile($cacheFile)
	{
		self::$cacheFile = $cacheFile;
	}

	public static function getCacheFile()
	{
		return (self::$cacheFile ?: rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . sprintf(static::defaultCacheFileName, md5(__FILE__)));
	}

	protected function resolveNamespaceAlias($class)
	{
		$class = strtolower($class);

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
		$class = strtolower($class);

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
		$class = strtolower($class);

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
		$class = strtolower($class);

		foreach ($this->classAliases as $alias => $target)
		{
			if ($target === $class)
			{
				return $alias;
			}
		}

		return null;
	}

	protected function handleNamespaceOfClass($class)
	{
		foreach ($this->directories as $namespace => $directories)
		{
			if (strpos($class, $namespace) === 0)
			{
				return true;
			}
		}

		return false;
	}
}

autoloader::set();
