<?php

namespace mageekguy\atoum\scripts;

require_once(__DIR__ . '/../../constants.php');

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class runner extends atoum\script
{
	protected $runner = null;
	protected $runTests = true;
	protected $scoreFile = null;
	protected $reportsEnabled = true;

	protected static $shutdownInstance = null;

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

	public function run(array $arguments = array())
	{
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

		parent::run($arguments);

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
	}

	public function version()
	{
		$this
			->writeMessage(sprintf($this->locale->_('Atoum version %s by %s (%s)'), atoum\version, atoum\author, atoum\directory) . PHP_EOL)
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
					'-ncc, --no-code-coverage' => $this->locale->_('Disable code coverage'),
					'-sf <file>, --score-file <file>' => $this->locale->_('Save score in <file>'),
					'-c <files>, --configuration-files <files>' => $this->locale->_('Use configuration <files>'),
					'-t <files>, --test-files <files>' => $this->locale->_('Use test files'),
					'-d <directories>, --directories <directories>' => $this->locale->_('Use test files in <directories>'),
					'-drt <string>, --default-report-title <string>' => $this->locale->_('Define default report title'),
					'--testIt' => $this->locale->_('Execute all Atoum unit tests')
				)
				,
				$options
			)
		);

		$this->runTests = false;

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

	public static function getShutdownInstance()
	{
		return self::$shutdownInstance;
	}

	public static function runAtShutdown($name)
	{
		if (self::$shutdownInstance !== null)
		{
			throw new exceptions\runtime('Unable to run \'' . $name . '\' at shutdown because \'' . self::$shutdownInstance->getName() . '\' will be run at shutdown');
		}

		$runnerScript = self::$shutdownInstance = new static($name);

		register_shutdown_function(function() use ($runnerScript) {
				set_error_handler(function($error, $message, $file, $line) use ($runnerScript) {
						if (error_reporting() !== 0)
						{
							$runnerScript->writeError($message . ' ' . $file . ' ' . $line);

							exit(2);
						}
					}
				);

				try
				{
					$runnerScript->run();
				}
				catch (\exception $exception)
				{
					$runnerScript->writeError($exception->getMessage());

					exit(3);
				}

				$score = $runnerScript->getRunner()->getScore();

				exit($score->getFailNumber() <= 0 && $score->getErrorNumber() <= 0 && $score->getExceptionNumber() <= 0 ? 0 : 1);
			}
		);

		return $runnerScript;
	}

	protected function includeFile($path)
	{
		@include_once($path);

		if (in_array(realpath((string) $path), get_included_files(), true) === false)
		{
			throw new exceptions\logic\invalidArgument(sprintf($this->getLocale()->_('Unable to include \'%s\''), $path));
		}

		return $this;
	}
}

?>
