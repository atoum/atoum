<?php

namespace mageekguy\atoum\scripts\phar;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions;

class stub extends atoum\script
{
	protected $pharName = 'phar://';

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $locale, $adapter);

		$this->pharName .= $this->getName();
	}

	public function run(array $arguments = array())
	{
		if (realpath($_SERVER['argv'][0]) !== $this->getName())
		{
			require_once($this->pharName . '/scripts/runners/autorunner.php');
		}
		else
		{
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

					$script->infos();
					},
					array('-i', '--infos')
					);

			$this->argumentsParser->addHandler(
					function($script, $argument, $values) {
					if (sizeof($values) !== 0)
					{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->signature();
					},
					array('-s', '--signature')
					);

			$this->argumentsParser->addHandler(
					function($script, $argument, $values) {
					if (sizeof($values) !== 1)
					{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->extractTo($values[0]);
					},
					array('-e', '--extractTo')
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
		}

		return $this;
	}

	public function help()
	{
		$this->writeMessage(sprintf($this->locale->_('Usage: %s [options]') . PHP_EOL, $this->getName()));
		$this->writeMessage(sprintf($this->locale->_('Atoum version %s by %s.'), atoum\test::getVersion(), atoum\test::author) . PHP_EOL);
		$this->writeMessage($this->locale->_('Available options are:') . PHP_EOL);

		$options = array(
				'-h, --help' => $this->locale->_('Display this help'),
				'-v, --version' => $this->locale->_('Display version'),
				'-i, --infos' => $this->locale->_('Display informations'),
				'-s, --signature' => $this->locale->_('Display phar signature'),
				'-e <dir>, --extract <dir>' => $this->locale->_('Extract all file from phar in <dir>'),
				'--testIt' => $this->locale->_('Execute all Atoum unit tests')
				);

		$this->writeLabels($options);

		return $this;
	}

	public function version()
	{
		$this->writeMessage(sprintf($this->locale->_('Atoum version %s by %s.'), atoum\test::getVersion(), atoum\test::author) . PHP_EOL);

		return $this;
	}

	public function infos()
	{
		$phar = new \Phar($this->pharName);

		$this->writeMessage($this->locale->_('Informations:') . PHP_EOL);
		$this->writeLabels($phar->getMetadata());

		return $this;
	}

	public function signature()
	{
		$phar = new \Phar($this->pharName);

		$signature = $phar->getSignature();

		$this->writeLabel($this->locale->_('Signature'), $signature['hash']);

		return $this;
	}

	public function extractTo($directory)
	{
		if (is_dir($directory) === false)
		{
			throw new exceptions\logic('Path \'' . $directory . '\' is not a directory');
		}

		if (is_writable($directory) === false)
		{
			throw new exceptions\logic('Directory \'' . $directory . '\' is not writable');
		}

		$phar = new \Phar($this->getName());

		$phar->extractTo($directory);

		return $this;
	}

	public function testIt()
	{
		define('\mageekguy\atoum\runners\autorun', false);

		$runner = new atoum\runner();

		foreach (new \recursiveIteratorIterator(new atoum\runners\directory\filter(new \recursiveDirectoryIterator($this->pharName . '/tests/units/classes'))) as $file)
		{
			require_once($file->getPathname());
		}

		$report = new atoum\reports\cli();
		$report->addRunnerField(new atoum\report\fields\runner\version\string(), array(atoum\runner::runStart));
		$report->addTestField(new atoum\report\fields\test\run\string(), array(atoum\test::runStart));
		$report->addTestField(new atoum\report\fields\test\event\string());
		$report->addTestField(new atoum\report\fields\test\duration\string(), array(atoum\test::runStop));
		$report->addTestField(new atoum\report\fields\test\memory\string(), array(atoum\test::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\result\string(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\tests\duration\string(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\tests\memory\string(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\duration\string(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\failures\string(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\outputs\string(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\errors\string(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\exceptions\string(), array(atoum\runner::runStop));

		$runner->addObserver($report)->run();

		return $this;
	}
}

?>
