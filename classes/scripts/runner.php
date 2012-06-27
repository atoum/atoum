<?php

namespace mageekguy\atoum\scripts;

require_once __DIR__ . '/../../constants.php';

use
	mageekguy\atoum,
	mageekguy\atoum\system,
	mageekguy\atoum\exceptions
;

class runner extends atoum\script
{
	const defaultConfigFile = '.atoum.php';

	protected $runner = null;
	protected $includer = null;
	protected $runTests = true;
	protected $scoreFile = null;
	protected $arguments = array();
	protected $namespaces = array();
	protected $tags = array();
	protected $methods = array();
	protected $loop = false;

	protected static $autorunner = null;

	public function __construct($name, atoum\factory $factory = null)
	{
		parent::__construct($name, $factory);

		$this
			->setIncluder($this->factory['atoum\includer']())
			->setRunner($this->factory['atoum\runner']($this->factory))
		;

		$this->factory['atoum\includer'] = $this->includer;
	}

	public function isRunningFromCli()
	{
		return (isset($_SERVER['argv']) === true && isset($_SERVER['argv'][0]) === true && realpath($_SERVER['argv'][0]) === $this->getName());
	}

	public function setRunner(atoum\runner $runner)
	{
		$this->runner = $runner;

		return $this->setArgumentHandlers();
	}

	public function getRunner()
	{
		return $this->runner;
	}

	public function setIncluder(atoum\includer $includer)
	{
		$this->includer = $includer;

		return $this;
	}

	public function getIncluder()
	{
		return $this->includer;
	}

	public function setScoreFile($path)
	{
		$this->scoreFile = (string) $path;

		return $this;
	}

	public function getScoreFile()
	{
		return $this->scoreFile;
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	public function setArguments(array $arguments)
	{
		$this->arguments = $arguments;

		return $this;
	}

	public function run(array $arguments = array())
	{
		try
		{
			$this->useDefaultConfigFiles();

			if (parent::run($arguments ?: $this->arguments)->runTests === true)
			{
				if ($this->loop === true)
				{
					$this->loop();
				}
				else
				{
					if ($this->runner->hasReports() === false)
					{
						$report = $this->factory['mageekguy\atoum\reports\realtime\cli']($this->factory);
						$report->addWriter($this->factory['mageekguy\atoum\writers\std\out']());

						$this->runner->addReport($report);
					}

					$methods = $this->methods;

					$oldFailMethods = array();

					if ($this->scoreFile !== null && ($scoreFileContents = @file_get_contents($this->scoreFile)) !== false && ($oldScore = @unserialize($scoreFileContents)) instanceof atoum\score)
					{
						$oldFailMethods = self::getFailMethods($oldScore);

						if ($oldFailMethods)
						{
							$methods = $oldFailMethods;
						}
					}

					$this->saveScore($newScore = $this->runner->run($this->namespaces, $this->tags, self::getClassesOf($methods), $methods));

					if ($oldFailMethods)
					{
						if (sizeof(self::getFailMethods($newScore)) <= 0)
						{
							$testMethods = $this->runner->getTestMethods($this->namespaces, $this->tags, $this->methods);

							if (sizeof($testMethods) > 1 || sizeof(current($testMethods)) > 1)
							{
								$this->saveScore($this->runner->run($this->namespaces, $this->tags, self::getClassesOf($this->methods), $this->methods));
							}
						}
					}
				}
			}
		}
		catch (atoum\exception $exception)
		{
			$this->writeError($exception->getMessage());

			exit(2);
		}

		return $this;
	}

	public function version()
	{
		$this
			->writeMessage(sprintf($this->locale->_('atoum version %s by %s (%s)'), atoum\version, atoum\author, atoum\directory) . PHP_EOL)
		;

		$this->runTests = false;

		return $this;
	}

	public function help()
	{
		$this->runTests = false;

		return parent::help();
	}

	public function useConfigFile($path)
	{
		$script = $this->factory['atoum\configurator']($this);

		$runner = $this->runner;

		try
		{
			$this->includer->includePath($path, function($path) use ($script, $runner) { include_once($path); });
		}
		catch (atoum\includer\exception $exception)
		{
			throw new atoum\includer\exception(sprintf($this->getLocale()->_('Unable to find configuration file \'%s\''), $path));
		}

		return $this;
	}

	public function useDefaultConfigFiles($startDirectory = null)
	{
		if ($startDirectory === null)
		{
			$startDirectory = atoum\directory;
		}

		foreach (self::getSubDirectoryPath($startDirectory) as $directory)
		{
			try
			{
				$this->useConfigFile($directory . self::defaultConfigFile);
			}
			catch (atoum\includer\exception $exception) {}
		}

		return $this;
	}

	public function testIt()
	{
		$this->runner->addTestsFromDirectory(atoum\directory . '/tests/units/classes');

		return $this;
	}

	public function enableLoopMode()
	{
		if ($this->loop !== null)
		{
			$this->loop = true;
		}

		return $this;
	}

	public function disableLoopMode()
	{
		$this->loop = null;

		return $this;
	}

	public function testNamespaces(array $namespace)
	{
		$this->namespaces = $namespace;

		return $this;
	}

	public function testTags(array $tags)
	{
		$this->tags = $tags;

		return $this;
	}

	public function testMethod($class, $method)
	{
		$this->methods[$class][] = $method;

		return $this;
	}

	public static function getAutorunner()
	{
		return self::$autorunner;
	}

	public static function autorun($name)
	{
		if (self::$autorunner !== null)
		{
			throw new exceptions\runtime('Unable to autorun \'' . $name . '\' because \'' . self::$autorunner->getName() . '\' is already set as autorunner');
		}

		$autorunner = self::$autorunner = new static($name);

		register_shutdown_function(function() use ($autorunner) {
				$score = $autorunner->run()->getRunner()->getScore();

				exit($score->getFailNumber() <= 0 && $score->getErrorNumber() <= 0 && $score->getExceptionNumber() <= 0 ? 0 : 1);
			}
		);

		return $autorunner;
	}

	public static function getSubDirectoryPath($directory, $directorySeparator = null)
	{
		$directorySeparator = $directorySeparator ?: DIRECTORY_SEPARATOR;

		$paths = array();

		if ($directory != '')
		{
			if ($directory == $directorySeparator)
			{
				$paths[] = $directory;
			}
			else
			{
				$directory = rtrim($directory, $directorySeparator);

				$path = '';

				foreach (explode($directorySeparator, $directory) as $subDirectory)
				{
					$path .= $subDirectory . $directorySeparator;

					$paths[] = $path;
				}
			}
		}

		return $paths;
	}

	protected function setArgumentHandlers()
	{
		if (parent::setArgumentHandlers()->runner !== null)
		{
			$this->getArgumentsParser()->resetHandlers();

			$this
				->addArgumentHandler(
						function($script, $argument, $values) {
							if (sizeof($values) !== 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->help();
						},
						array('-h', '--help'),
						null,
						$this->locale->_('Display this help')
					)
				->addArgumentHandler(
						function($script, $argument, $values) {
							if (sizeof($values) !== 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->version();
						},
						array('-v', '--version'),
						null,
						$this->locale->_('Display version')
					)
				->addArgumentHandler(
						function($script, $argument, $path) {
							if (sizeof($path) != 1)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->getRunner()->setPhpPath(current($path));
						},
						array('-p', '--php'),
						'<path/to/php/binary>',
						$this->locale->_('Path to PHP binary which must be used to run tests')
					)
				->addArgumentHandler(
						function($script, $argument, $defaultReportTitle) {
							if (sizeof($defaultReportTitle) != 1)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->getRunner()->setDefaultReportTitle(current($defaultReportTitle));
						},
						array('-drt', '--default-report-title'),
						'<string>',
						$this->locale->_('Define default report title with <string>')
					)
				->addArgumentHandler(
						function($script, $argument, $files) {
							if (sizeof($files) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							foreach ($files as $path)
							{
								try
								{
									$script->useConfigFile($path);
								}
								catch (includer\exception $exception)
								{
									throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Configuration file \'%s\' does not exist'), $path));
								}
							}
						},
						array('-c', '--configuration-files'),
						'<file>...',
						$this->locale->_('Use all configuration files <file>'),
						1
					)
				->addArgumentHandler(
						function($script, $argument, $file) {
							if (sizeof($file) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->setScoreFile(current($file));
						},
						array('-sf', '--score-file'),
						'<file>',
						$this->locale->_('Save score in file <file>')
					)
				->addArgumentHandler(
						function($script, $argument, $maxChildrenNumber) {
							if (sizeof($maxChildrenNumber) != 1)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->getRunner()->setMaxChildrenNumber(current($maxChildrenNumber));
						},
						array('-mcn', '--max-children-number'),
						'<integer>',
						$this->locale->_('Maximum number of sub-processus which will be run simultaneously')
					)
				->addArgumentHandler(
						function($script, $argument, $empty) {
							if ($empty)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->getRunner()->disableCodeCoverage();
						},
						array('-ncc', '--no-code-coverage'),
						null,
						$this->locale->_('Disable code coverage')
					)
				->addArgumentHandler(
						function($script, $argument, $directories) {
							if (sizeof($directories) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							foreach ($directories as $directory)
							{
								$script->getRunner()->getCoverage()->excludeDirectory($directory);
							}
						},
						array('-nccid', '--no-code-coverage-in-directories'),
						'<directory>...',
						$this->locale->_('Disable code coverage in directories <directory>')
					)
				->addArgumentHandler(
						function($script, $argument, $namespaces) {
							if (sizeof($namespaces) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							foreach ($namespaces as $namespace)
							{
								$script->getRunner()->getCoverage()->excludeNamespace($namespace);
							}
						},
						array('-nccfns', '--no-code-coverage-for-namespaces'),
						'<namespace>...',
						$this->locale->_('Disable code coverage for namespaces <namespace>')
					)
				->addArgumentHandler(
						function($script, $argument, $classes) {
							if (sizeof($classes) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							foreach ($classes as $class)
							{
								$script->getRunner()->getCoverage()->excludeClass($class);
							}
						},
						array('-nccfc', '--no-code-coverage-for-classes'),
						'<class>...',
						$this->locale->_('Disable code coverage for classes <class>')
					)
				->addArgumentHandler(
						function($script, $argument, $files) {
							if (sizeof($files) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$runner = $script->getRunner();

							foreach ($files as $path)
							{
								$runner->addTest($path);
							}
						},
						array('-f', '--test-files'),
						'<file>...',
						$this->locale->_('Execute all unit test files <file>')
					)
				->addArgumentHandler(
						function($script, $argument, $directories) {
							if (sizeof($directories) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$runner = $script->getRunner();

							foreach ($directories as $directory)
							{
								$runner->addTestsFromDirectory($directory);
							}
						},
						array('-d', '--directories'),
						'<directory>...',
						$this->locale->_('Execute unit test files in all <directory>')
					)
				->addArgumentHandler(
						function($script, $argument, $extensions) {
							if (sizeof($extensions) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->getRunner()->getTestDirectoryIterator()->acceptExtensions($extensions);
						},
						array('-tfe', '--test-file-extensions'),
						'<extension>...',
						$this->locale->_('Execute unit test files with one of extensions <extension>')
					)
				->addArgumentHandler(
						function($script, $argument, $patterns) {
							if (sizeof($patterns) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$runner = $script->getRunner();

							foreach ($patterns as $pattern)
							{
								$runner->addTestsFromPattern($pattern);
							}
						},
						array('-g', '--glob'),
						'<pattern>...',
						$this->locale->_('Execute unit test files which match <pattern>')
					)
				->addArgumentHandler(
						function($script, $argument, $tags) {
							if (sizeof($tags) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->testTags($tags);
						},
						array('-t', '--tags'),
						'<tag>...',
						$this->locale->_('Execute only unit test with tags <tag>')
					)
				->addArgumentHandler(
						function($script, $argument, $methods) {
							if (sizeof($methods) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							foreach ($methods as $method)
							{
								$method = explode('::', $method);

								if (sizeof($method) != 2)
								{
									throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
								}

								$script->testMethod($method[0], $method[1]);
							}
						},
						array('-m', '--methods'),
						'<class::method>...',
						$this->locale->_('Execute all <class::method>, * may be used as wildcard for class name or method name')
					)
				->addArgumentHandler(
						function($script, $argument, $namespaces) {
							if (sizeof($namespaces) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->testNamespaces($namespaces);
						},
						array('-ns', '--namespaces'),
						'<namespace>...',
						$this->locale->_('Execute all classes in all namespaces <namespace>')
					)
				->addArgumentHandler(
						function($script, $argument, $values) {
							if ($values)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->enableLoopMode();
						},
						array('-l', '--loop'),
						null,
						$this->locale->_('Execute tests in an infinite loop')
					)
				->addArgumentHandler(
						function($script, $argument, $values) {
							if (sizeof($values) !== 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->disableLoopMode();
						},
						array('--disable-loop-mode'),
						null,
						null,
						3
					)
				->addArgumentHandler(
						function($script, $argument, $values) {
							if (sizeof($values) !== 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->testIt();
						},
						array('--test-it'),
						null,
						$this->locale->_('Execute atoum unit tests')
					)
				->addArgumentHandler(
						function($script, $argument, $values) {
							if ($values)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							\mageekguy\atoum\cli::forceTerminal();
						},
						array('-ft', '--force-terminal'),
						null,
						$this->locale->_('Force output as in terminal')
					)
				->addArgumentHandler(
						function($script, $argument, $values) {
							if (sizeof($values) != 1)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$script->getRunner()->setBootstrapFile($values[0]);
						},
						array('-bf', '--bootstrap-file'),
						'<file>',
						$this->locale->_('Include <file> before executing each test method'),
						2
					)
				->addArgumentHandler(
						function($script, $argument, $values) {
							if (sizeof($values) != 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$report = $script->getFactory()->build('mageekguy\atoum\reports\realtime\cli\light', array($script->getFactory()));
							$report->addWriter($script->getFactory()->build('mageekguy\atoum\writers\std\out'));

							$script->getRunner()->addReport($report);
						},
						array('-ulr', '--use-light-report'),
						null,
						$this->locale->_('Use "light" CLI report')
					)
			;
		}

		return $this;
	}

	protected function runAgain()
	{
		return ($this->prompt($this->locale->_('Press <Enter> to reexecute, press any other key and <Enter> to stop...')) == '');
	}

	protected function loop()
	{
		$arguments = ' --disable-loop-mode';

		$cli = $this->factory['mageekguy\atoum\cli']();

		if ($cli->isTerminal() === true)
		{
			$arguments .= ' --force-terminal';
		}

		$addScoreFile = false;

		foreach ($this->getArgumentsParser()->getValues() as $argument => $values)
		{
			switch ($argument)
			{
				case '-l':
				case '--loop':
				case '--disable-loop-mode':
					break;

				case '-sf':
				case '--score-file':
					$addScoreFile = true;
					break;

				default:
					$arguments .= ' ' . $argument;

					if (sizeof($values) > 0)
					{
						$arguments .= ' ' . join(' ', $values);
					}
			}
		}

		if ($this->scoreFile === null)
		{
			$this->scoreFile = sys_get_temp_dir() . '/atoum.score';

			@unlink($this->scoreFile);

			$addScoreFile = true;
		}

		if ($addScoreFile === true)
		{
			$arguments .= ' --score-file ' . $this->scoreFile;
		}

		if ($this->isRunningFromCli() === false)
		{
			$declaredTestClasses = $this->runner->getDeclaredTestClasses();

			if (sizeof($declaredTestClasses) > 0)
			{
				$files = array();

				foreach ($declaredTestClasses as $declaredTestClass)
				{
					$declaredTestClass = $this->factory['reflectionClass']($declaredTestClass);

					$file = $declaredTestClass->getFilename();

					if (in_array($file, $files) === false)
					{
						$files[] = $file;
					}
				}

				$arguments .= ' -f ' . join(' ', $files);
			}
		}

		$command = $this->runner->getPhpPath() . ' ' . $this->getName() . $arguments;

		while ($this->runTests === true)
		{
			$php = proc_open(
				escapeshellcmd($command),
				array(
					1 => array('pipe', 'w'),
					2 => array('pipe', 'w')
				),
				$pipes
			);

			stream_set_blocking($pipes[1], 0);
			stream_set_blocking($pipes[2], 0);

			$null = null;

			while (feof($pipes[1]) === false && feof($pipes[2]) === false)
			{
				$updatedPipes = $pipes;

				$pipesUpdated = stream_select($updatedPipes, $null, $null, null);

				if ($pipesUpdated !== false)
				{
					foreach ($updatedPipes as $pipe)
					{
						switch ($pipe)
						{
							case $pipes[1]:
								$this->outputWriter->write(stream_get_contents($pipe));
								break;

							default:
								$this->errorWriter->write(stream_get_contents($pipe));
						}

					}
				}
			}

			if ($this->loop === false || $this->runAgain() === false)
			{
				$this->runTests = false;
			}
		}

		return $this;
	}

	protected function saveScore(atoum\score $score)
	{
		if ($this->scoreFile !== null && $this->adapter->file_put_contents($this->scoreFile, serialize($score), \LOCK_EX) === false)
		{
			throw new exceptions\runtime('Unable to save score in \'' . $this->scoreFile . '\'');
		}

		return $this;
	}

	protected static function getClassesOf($methods)
	{
		return sizeof($methods) <= 0 || isset($methods['*']) === true ? array() : array_keys($methods);
	}

	private static function getFailMethods(atoum\score $score)
	{
		return self::mergeMethods(self::mergeMethods(self::mergeMethods($score->getMethodsWithFail(), $score->getMethodsWithError()), $score->getMethodsWithException()), $score->getMethodsNotCompleted());
	}

	private static function mergeMethods(array $methods, array $newMethods)
	{
		foreach ($newMethods as $class => $classMethods)
		{
			if (isset($methods[$class]) === false)
			{
				$methods[$class] = $classMethods;
			}
			else
			{
				$methods[$class] = array_unique(array_merge($methods[$class], $classMethods));
			}
		}

		return $methods;
	}
}
