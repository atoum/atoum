<?php

namespace mageekguy\atoum\phar;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions;

class stub extends atoum\script
{
	protected $pharName = 'phar://';
	protected $help = false;
	protected $version = false;
	protected $infos = false;
	protected $signature = false;
	protected $decompress = false;
	protected $extract = false;
	protected $testIt = false;

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $locale, $adapter);

		$this->pharName .= $this->getName();
	}

	public function run(atoum\superglobal $superglobal = null)
	{
		if (PHP_SAPI !== 'cli' || realpath($_SERVER['argv'][0]) !== $this->getName())
		{
			require_once($this->pharName . '/scripts/runners/autorunner.php');
		}
		else
		{
			parent::run($superglobal);

			if ($this->help === true)
			{
				$this->help();
			}

			if ($this->version === true)
			{
				$this->version();
			}

			if ($this->infos === true)
			{
				$this->infos();
			}

			if ($this->signature === true)
			{
				$this->signature();
			}

			if ($this->testIt === true)
			{
				$this->testIt();
			}

			if ($this->extract !== false)
			{
				$this->extract();
			}
		}

		return $this;
	}

	protected function handleArgument($argument)
	{
		switch ($argument)
		{
			case '-h':
			case '--help':
				$this->help = true;
				break;

			case '-v':
			case '--version':
				$this->version = true;
				break;

			case '-i':
			case '--infos':
				$this->infos = true;
				break;

			case '-s':
			case '--signature':
				$this->signature = true;
				break;

			case '--testIt':
				$this->testIt = true;
				break;

			case '-e':
			case '--extract':
				$this->arguments->next();
				$directory = $this->arguments->current();

				if ($this->arguments->valid() === false || self::isArgument($directory) === true)
				{
					throw new exceptions\logic\invalidArgument('Bad usage of ' . $argument . ', do php ' . $this->getName() . ' --help for more informations');
				}

				$this->extract = $directory;
				break;

			default:
				throw new exceptions\logic\invalidArgument('Argument \'' . $argument . '\' is unknown');
		}
	}

	protected function help()
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

	protected function version()
	{
		$this->writeMessage(sprintf($this->locale->_('Atoum version %s by %s.'), atoum\test::getVersion(), atoum\test::author) . PHP_EOL);

		return $this;
	}

	protected function infos()
	{
		$phar = new \Phar($this->pharName);

		$this->writeMessage($this->locale->_('Informations:') . PHP_EOL);
		$this->writeLabels($phar->getMetadata());

		return $this;
	}

	protected function signature()
	{
		$phar = new \Phar($this->pharName);

		$signature = $phar->getSignature();

		$this->writeLabel($this->locale->_('Signature'), $signature['hash']);

		return $this;
	}

	protected function extract()
	{
		if (is_dir($this->extract) === false)
		{
			throw new exceptions\logic('Path \'' . $this->extract . '\' is not a directory');
		}

		if (is_writable($this->extract) === false)
		{
			throw new exceptions\logic('Directory \'' . $this->extract . '\' is not writable');
		}

		$phar = new \Phar($this->getName());

		$phar->extractTo($this->extract);

		return $this;
	}

	protected function testIt()
	{
		define('\mageekguy\atoum\runners\autorun', false);

		$runner = new atoum\runner();

		foreach (new \recursiveIteratorIterator(new atoum\runners\directory\filter(new \recursiveDirectoryIterator($this->pharName . '/tests/units/classes'))) as $file)
		{
			require($file->getPathname());
		}

		$stringDecorator = new atoum\report\decorators\string();
		$stringDecorator->addWriter(new atoum\writers\stdout());

		$report = new atoum\report();
		$report->addRunnerField(new atoum\report\fields\runner\version(), array(atoum\runner::runStart));
		$report->addTestField(new atoum\report\fields\test\run(), array(atoum\test::runStart));
		$report->addTestField(new atoum\report\fields\test\event());
		$report->addTestField(new atoum\report\fields\test\duration(), array(atoum\test::runStop));
		$report->addTestField(new atoum\report\fields\test\memory(), array(atoum\test::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\result(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\tests\duration(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\tests\memory(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\duration(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\failures(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\outputs(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\errors(), array(atoum\runner::runStop));
		$report->addRunnerField(new atoum\report\fields\runner\exceptions(), array(atoum\runner::runStop));
		$report->addDecorator($stringDecorator);

		$runner->addObserver($report)->run();

		return $this;
	}
}

?>
