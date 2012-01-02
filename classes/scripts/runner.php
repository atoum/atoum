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
			->setRunner($this->factory->build('atoum\runner'))
			->setIncluder($this->factory->build('atoum\includer'))
		;
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
			parent::run($arguments ?: $this->arguments);

			if ($this->runTests === true)
			{
				$this->useDefaultConfigFile();

				if ($this->loop === true)
				{
					$this->loop();
				}
				else
				{
					if ($this->runner->hasReports() === false)
					{
						$report = new atoum\reports\realtime\cli();
						$report->addWriter(new atoum\writers\std\out());

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
		$runner = $this->runner;

		try
		{
			$this->includer->includePath($path, function($path) use ($runner) { include_once($path); });
		}
		catch (atoum\includer\exception $exception)
		{
			throw new atoum\includer\exception(sprintf($this->getLocale()->_('Unable to find configuration file \'%s\''), $path));
		}

		return $this;
	}

	public function useDefaultConfigFile()
	{
		try
		{
			$this->useConfigFile(atoum\directory . '/' . self::defaultConfigFile);
		}
		catch (atoum\includer\exception $exception) {};

		return $this;
	}

	public function testIt()
	{
		$this->runner->addTestsFromDirectory(atoum\directory . '/tests/units/classes');

		return $this;
	}

	public function enableLoop()
	{
		$this->loop = true;

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

	protected function setArgumentHandlers()
	{
		parent::setArgumentHandlers();

		if ($this->runner !== null)
		{
			$this->getArgumentsParser()->resetHandlers();

			$runner = $this->runner;

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
						function($script, $argument, $path) use ($runner) {
							if (sizeof($path) != 1)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$runner->setPhpPath(current($path));
						},
						array('-p', '--php'),
						'<path/to/php/binary>',
						$this->locale->_('Path to PHP binary which must be used to run tests')
					)
				->addArgumentHandler(
						function($script, $argument, $defaultReportTitle) use ($runner) {
							if (sizeof($defaultReportTitle) != 1)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$runner->setDefaultReportTitle(current($defaultReportTitle));
						},
						array('-drt', '--default-report-title'),
						'<string>',
						$this->locale->_('Define default report title with <string>')
					)
				->addArgumentHandler(
						function($script, $argument, $files) use ($runner) {
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
						$this->locale->_('Use all configuration files <file>')
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
						function($script, $argument, $maxChildrenNumber) use ($runner) {
							if (sizeof($maxChildrenNumber) != 1)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$runner->setMaxChildrenNumber(current($maxChildrenNumber));
						},
						array('-mcn', '--max-children-number'),
						'<integer>',
						$this->locale->_('Maximum number of sub-processus which will be run simultaneously')
					)
				->addArgumentHandler(
						function($script, $argument, $empty) use ($runner) {
							if ($empty)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$runner->disableCodeCoverage();
						},
						array('-ncc', '--no-code-coverage'),
						null,
						$this->locale->_('Disable code coverage')
					)
				->addArgumentHandler(
						function($script, $argument, $files) use ($runner) {
							if (sizeof($files) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

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
						function($script, $argument, $directories) use ($runner) {
							if (sizeof($directories) <= 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

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
						function($script, $argument, $tags) use ($runner) {
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

							$script->enableLoop();
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
						function($script, $argument, $values) use ($runner) {
							if (sizeof($values) != 1)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$bootstrapFile = realpath($values[0]);

							if ($bootstrapFile === false || is_file($bootstrapFile) === false || is_readable($bootstrapFile) === false)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bootstrap file \'%s\' does not exist'), $values[0]));
							}

							$runner->setBootstrapFile($bootstrapFile);
						},
						array('-bf', '--bootstrap-file'),
						'<file>',
						$this->locale->_('Include <file> before executing each test method')
					)
				->addArgumentHandler(
						function($script, $argument, $values) use ($runner) {
							if (sizeof($values) != 0)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
							}

							$report = new atoum\reports\realtime\cli\light();
							$report->addWriter(new atoum\writers\std\out());
							$runner->addReport($report);
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
		if ($this->scoreFile === null)
		{
			$this->scoreFile = sys_get_temp_dir() . '/atoum.score';
			@unlink($this->scoreFile);
		}

		while ($this->runTests === true)
		{
			$command = $this->runner->getPhpPath() . ' ' . join(' ', array_filter($_SERVER['argv'], function($value) { return ($value != '-l' && $value != '--loop'); })) . ' --score-file ' . $this->scoreFile;

			$cli = new atoum\cli();

			if ($cli->isTerminal() === true)
			{
				$command .= ' --force-terminal';
			}

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

	protected static function includeForRunner(atoum\runner $runner, $path)
	{
		include_once $path;
	}

	private static function getFailMethods(atoum\score $score)
	{
		return self::mergeMethods(self::mergeMethods($score->getMethodsWithFail(), $score->getMethodsWithError()), $score->getMethodsWithException());
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

?>
