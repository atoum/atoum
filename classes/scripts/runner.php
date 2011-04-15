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

	public function runTests($boolean)
	{
		$this->runTests = $boolean == true;

		return $this;
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

				$script
					->runTests(false)
					->version()
				;
			},
			array('-v', '--version')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script
					->runTests(false)
					->help()
				;
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

				foreach ($files as $file)
				{
					if (file_exists($file) === false)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Configuration file path \'%s\' is invalid'), $file));
					}

					if (is_readable($file) === false)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Unable to read configuration file \'%s\''), $file));
					}

					require_once($file);
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

				foreach ($files as $file)
				{
					if (is_file($file) === false)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Test file path \'%s\' is invalid'), $file));
					}

					if (is_readable($file) === false)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Unable to read test file \'%s\''), $file));
					}

					require_once($file);
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
					$directory = realpath($directory);

					if ($directory === false)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Path \'%s\' is invalid'), $directory));
					}

					if (is_dir($directory) === false)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Path \'%s\' is not a directory'), $directory));
					}

					if (is_readable($directory) === false)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Unable to read directory \'%s\''), $directory));
					}

					foreach (new \recursiveIteratorIterator(new atoum\src\iterator\filter(new \recursiveDirectoryIterator($directory))) as $file)
					{
						require_once($file->getPathname());
					}
				}
			},
			array('-d', '--directories')
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
					'-drt <string>, --default-report-title <string>' => $this->locale->_('Define default report title')
				)
				,
				$options
			)
		);

		return $this;
	}
}

?>
