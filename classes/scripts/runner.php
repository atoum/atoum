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
	protected $reportsEnabled = true;

	protected static $autorunner = null;
	protected static $runningRunner = null;

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $locale, $adapter);

		$this->setRunner(new atoum\runner());
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
		self::$runningRunner = $this;

		ini_set('log_errors_max_len', '0');
		ini_set('log_errors', 'Off');
		ini_set('display_errors', 'stderr');

		$runner = $this->runner;

		$this->argumentsParser->addHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->version();
			},
			array('-v', '--version')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->help();
			},
			array('-h', '--help')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $path) use ($runner) {
				if (sizeof($path) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$runner->setPhpPath(current($path));
			},
			array('-p', '--php')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $defaultReportTitle) use ($runner) {
				if (sizeof($defaultReportTitle) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$runner->setDefaultReportTitle(current($defaultReportTitle));
			},
			array('-drt', '--default-report-title')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $files) use ($runner) {
				if (sizeof($files) <= 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				foreach ($files as $path)
				{
					$script->includeFile($path);
				}
			},
			array('-c', '--configuration-files')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $file) {
				if (sizeof($file) <= 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setScoreFile(current($file));
			},
			array('-sf', '--score-file')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $maxChildrenNumber) use ($runner) {
				if (sizeof($maxChildrenNumber) > 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$runner->setMaxChildrenNumber(current($maxChildrenNumber));
			},
			array('-mcn', '--max-children-number')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $empty) use ($runner) {
				if (sizeof($empty) > 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$runner->disableCodeCoverage();
			},
			array('-ncc', '--no-code-coverage')
		);

		$this->argumentsParser->addHandler(
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
			array('-t', '--test-files')
		);

		$this->argumentsParser->addHandler(
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
			array('-d', '--directories')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->testIt();
			},
			array('--testIt')
		);

		parent::run(sizeof($arguments) > 0 ? $arguments : $this->arguments);

		if ($this->runTests === true)
		{
			if ($runner->hasReports() === false)
			{
				$report = new atoum\reports\realtime\cli();
				$report->addWriter(new atoum\writers\std\out());

				$runner->addReport($report);
			}

			$runner->run();

			if ($this->scoreFile !== null)
			{
				if ($this->adapter->file_put_contents($this->scoreFile, serialize($runner->getScore()), \LOCK_EX) === false)
				{
					throw new exceptions\runtime('Unable to save score in \'' . $this->scoreFile . '\'');
				}
			}
		}

		self::$runningRunner = null;
	}

	public function version()
	{
		$this
			->writeMessage(sprintf($this->locale->_('atoum version %s by %s (%s)'), atoum\version, atoum\author, atoum\directory) . PHP_EOL)
		;

		$this->runTests = false;

		return $this;
	}

	public function help(array $options = array())
	{
		$this
			->writeMessage(sprintf($this->locale->_('Usage: %s [options]'), $this->getName()) . PHP_EOL)
			->writeMessage($this->locale->_('Available options are:') . PHP_EOL)
		;

		$this->writeLabels(
			array_merge(
				array(
					'-h, --help' => $this->locale->_('Display this help'),
					'-v, --version' => $this->locale->_('Display version'),
					'-p <path/to/php/binary>, --php <path/to/php/binary>' => $this->locale->_('Path to PHP binary which must be used to run tests'),
					'-ncc, --no-code-coverage' => $this->locale->_('Disable code coverage'),
					'-mcn, --max-children-number <integer>' => $this->locale->_('Maximum number of sub-processus which will be run simultaneously'),
					'-sf <file>, --score-file <file>' => $this->locale->_('Save score in <file>'),
					'-c <files>, --configuration-files <files>' => $this->locale->_('Use configuration <files>'),
					'-t <files>, --test-files <files>' => $this->locale->_('Use test files'),
					'-d <directories>, --directories <directories>' => $this->locale->_('Use test files in <directories>'),
					'-drt <string>, --default-report-title <string>' => $this->locale->_('Define default report title'),
					'--testIt' => $this->locale->_('Execute all atoum unit tests')
				)
				,
				$options
			)
		);

		$this->runTests = false;

		return $this;
	}

	public function includeFile($path)
	{
		$runner = $this->getRunner();

		include_once $path;

		if (in_array(realpath((string) $path), get_included_files(), true) === false)
		{
			throw new exceptions\logic\invalidArgument(sprintf($this->getLocale()->_('Unable to include \'%s\''), $path));
		}

		return $this;
	}

	public function runFile($path)
	{
		return $this->includeFile($path);
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
}

?>
