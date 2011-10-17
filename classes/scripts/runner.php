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
	protected $runner = null;
	protected $runTests = true;
	protected $scoreFile = null;
	protected $arguments = array();
	protected $namespaces = array();
	protected $tags = array();
	protected $methods = array();

	protected static $autorunner = null;

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		$this->setRunner($runner = new atoum\runner());

		parent::__construct($name, $locale, $adapter);
	}

	public function setRunner(atoum\runner $runner)
	{
		$this->runner = $runner;

		return $this;
	}

	public function getRunner()
	{
		return $this->runner;
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
		ini_set('log_errors_max_len', '0');
		ini_set('log_errors', 'Off');
		ini_set('display_errors', 'stderr');

		parent::run(sizeof($arguments) > 0 ? $arguments : $this->arguments);

		if ($this->runTests === true)
		{
			if ($this->runner->hasReports() === false)
			{
				$report = new atoum\reports\realtime\cli();
				$report->addWriter(new atoum\writers\std\out());

				$this->runner->addReport($report);
			}

			$this->runner->run($this->namespaces, $this->tags, sizeof($this->methods) <= 0 || isset($this->methods['*']) === true ? array() : array_keys($this->methods), $this->methods);

			if ($this->scoreFile !== null)
			{
				if ($this->adapter->file_put_contents($this->scoreFile, serialize($this->runner->getScore()), \LOCK_EX) === false)
				{
					throw new exceptions\runtime('Unable to save score in \'' . $this->scoreFile . '\'');
				}
			}
		}
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

	public function includeFile($path)
	{
		$runner = $this->getRunner();

		@include_once $path;

		if (in_array(realpath((string) $path), get_included_files(), true) === false)
		{
			throw new exceptions\logic\invalidArgument(sprintf($this->getLocale()->_('Unable to include \'%s\''), $path));
		}

		return $this;
	}

	public function runFile($path)
	{
		try
		{
			return $this->includeFile($path);
		}
		catch (\exception $exception)
		{
			throw new exceptions\logic\invalidArgument(sprintf($this->getLocale()->_('Test file \'%s\' does not exist'), $path));
		}
	}

	public function runDirectory($directory)
	{
		try
		{
			foreach (new \recursiveIteratorIterator(new atoum\src\iterator\filter(new \recursiveDirectoryIterator($directory))) as $path)
			{
				$this->runFile($path);
			}
		}
		catch (exceptions\logic\invalidArgument $exception)
		{
			throw $exception;
		}
		catch (\exception $exception)
		{
			throw new exceptions\logic\invalidArgument(sprintf($this->getLocale()->_('Unable to read directory \'%s\''), $directory));
		}

		return $this;
	}

	public function testIt()
	{
		return $this->runDirectory(atoum\directory . '/tests/units/classes');
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
				set_error_handler(function($error, $message, $file, $line) use ($autorunner) {
						if (error_reporting() !== 0)
						{
							$autorunner->writeError($message . ' ' . $file . ' ' . $line);

							exit(2);
						}
					}
				);

				try
				{
					$autorunner->run();
				}
				catch (\exception $exception)
				{
					$autorunner->writeError($exception->getMessage());

					exit(3);
				}

				$score = $autorunner->getRunner()->getScore();

				exit($score->getFailNumber() <= 0 && $score->getErrorNumber() <= 0 && $score->getExceptionNumber() <= 0 ? 0 : 1);
			}
		);

		return $autorunner;
	}

	protected function setArgumentHandlers()
	{
		$runner = $this->runner;

		$this->addArgumentHandler(
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
		);

		$this->addArgumentHandler(
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
		);

		$this->addArgumentHandler(
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
		);

		$this->addArgumentHandler(
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
		);

		$this->addArgumentHandler(
			function($script, $argument, $files) use ($runner) {
				if (sizeof($files) <= 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				foreach ($files as $path)
				{
					try
					{
						$script->includeFile($path);
					}
					catch (\exception $exception)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Configuration file \'%s\' does not exist'), $path));
					}
				}
			},
			array('-c', '--configuration-files'),
			'<file>...',
			$this->locale->_('Use all configuration files <file>')
		);

		$this->addArgumentHandler(
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
		);

		$this->addArgumentHandler(
			function($script, $argument, $maxChildrenNumber) use ($runner) {
				if (sizeof($maxChildrenNumber) > 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$runner->setMaxChildrenNumber(current($maxChildrenNumber));
			},
			array('-mcn', '--max-children-number'),
			'<integer>',
			$this->locale->_('Maximum number of sub-processus which will be run simultaneously')
		);

		$this->addArgumentHandler(
			function($script, $argument, $empty) use ($runner) {
				if (sizeof($empty) > 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$runner->disableCodeCoverage();
			},
			array('-ncc', '--no-code-coverage'),
			null,
			$this->locale->_('Disable code coverage')
		);

		$this->addArgumentHandler(
			function($script, $argument, $files) {
				if (sizeof($files) <= 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				foreach ($files as $path)
				{
					$script->runFile($path);
				}
			},
			array('-f', '--test-files'),
			'<file>...',
			$this->locale->_('Execute all unit test files <file>')
		);

		$this->addArgumentHandler(
			function($script, $argument, $directories) {
				if (sizeof($directories) <= 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				foreach ($directories as $directory)
				{
					$script->runDirectory($directory);
				}
			},
			array('-d', '--directories'),
			'<directory>...',
			$this->locale->_('Execute unit test files in all <directory>')
		);

		$this->addArgumentHandler(
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
		);

		$this->addArgumentHandler(
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
		);

		$this->addArgumentHandler(
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
		);

		$this->addArgumentHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->testIt();
			},
			array('--testIt'),
			null,
			$this->locale->_('Execute atoum unit tests')
		);

		return $this;
	}
}

?>
