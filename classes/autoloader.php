<?php

namespace mageekguy\atoum;

class autoloader
{
	const version = 1;
	const defaultFileSuffix = '.php';
	const defaultCacheFileName = '%s.atoum.cache';

	protected $version = null;
	protected $classes = array();
	protected $directories = array();
	protected $classAliases = array();
	protected $namespaceAliases = array();
	protected $cacheFileInstance = null;

	protected static $autoloader = null;

	private $cacheUsed = false;

	private static $cacheFile = null;
	private static $registeredAutoloaders = null;

	public function __construct(array $namespaces = array(), array $namespaceAliases = array(), $classAliases = array())
	{
		$this->version = static::version;

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

		foreach ($classAliases ?: array('atoum' => __NAMESPACE__ . '\test', __NAMESPACE__ => __NAMESPACE__ . '\test') as $alias => $target)
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

		if (self::$registeredAutoloaders === null)
		{
			self::$registeredAutoloaders = new \splObjectStorage();
		}

		self::$registeredAutoloaders->attach($this);

		return $this;
	}

	public function unregister()
	{
		if (spl_autoload_unregister(array($this, 'requireClass')) === false)
		{
			throw new \runtimeException('Unable to unregister');
		}

		self::$registeredAutoloaders->detach($this);

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
		$this->readCache();

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

				$this->writeCache();

				$path = (isset($this->classes[$class]) === false ? null : $this->classes[$class]);
			}
		}

		return $path;
	}

	public function requireClass($class)
	{
		$class = strtolower($class);

		if (static::exists($class) === false && ($path = $this->getPath($class)) !== null)
		{
			$realClass = $class;

			require $path;
		}
		else
		{
			$realClass = $this->resolveClassAlias($class);

			if (static::exists($realClass) === false && ($path = $this->getPath($realClass)) !== null)
			{
				require $path;
			}
			else
			{
				$realClass = $this->resolveNamespaceAlias($realClass);

				if (static::exists($realClass) === false && ($path = $this->getPath($realClass)) !== null)
				{
					require $path;
				}
			}
		}

		if (static::exists($realClass) === false && ($path = $this->getPath($realClass)) !== null)
		{
			require $path;
		}

		if (static::exists($realClass) === true)
		{
			$alias = ($realClass !== $class ? $class : $this->getClassAlias($realClass) ?: $this->getNamespaceAlias($realClass));

			if ($alias !== null)
			{
				class_alias($realClass, $alias);
			}
		}
	}

	public function setCacheFileForInstance($cacheFile)
	{
		$this->cacheFileInstance = $cacheFile;

		return $this;
	}

	public function getCacheFileForInstance()
	{
		return ($this->cacheFileInstance ?: static::getCacheFile());
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

	public static function getRegisteredAutoloaders()
	{
		$registeredAutoloaders = array();

		foreach (self::$registeredAutoloaders as $autoloader)
		{
			$registeredAutoloaders[] = $autoloader;
		}

		return $registeredAutoloaders;
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

	protected function readCache()
	{
		if ($this->cacheUsed === false)
		{
			$cacheContents = @file_get_contents($this->getCacheFileForInstance());

			if ($cacheContents !== false)
			{
				$cacheContents = @unserialize($cacheContents) ?: null;
			}

			if (is_array($cacheContents) === true && isset($cacheContents['version']) === true && $cacheContents['version'] === static::version)
			{
				$this->classes = $cacheContents['classes'];
			}

			$this->cacheUsed = true;
		}

		return $this;
	}

	protected function writeCache()
	{
		$cacheFile = $this->getCacheFileForInstance();

		if (@file_put_contents($cacheFile, serialize(array('version' => static::version, 'classes' => $this->classes))) === false)
		{
			throw new \runtimeException('Unable to write in  \'' . $cacheFile . '\'');
		}

		return $this;
	}

	protected static function exists($class)
	{
		return (
			class_exists($class, false) === true ||
			interface_exists($class, false) === true || (
				version_compare(PHP_VERSION, '5.4.0') >= 0 &&
				trait_exists($class, false) === true
			)
		);
	}
}

autoloader::set();
