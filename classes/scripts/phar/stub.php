<?php

namespace mageekguy\atoum\scripts\phar;

use \mageekguy\atoum;
use \mageekguy\atoum\scripts;
use \mageekguy\atoum\exceptions;

class stub extends atoum\scripts\runner
{
	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $locale, $adapter);

		$this->name = \phar::running(false);
	}

	public function run(array $arguments = array())
	{
		$this->argumentsParser->addHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script
					->runTests(false)
					->infos()
				;
			},
			array('-i', '--infos')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script
					->runTests(false)
					->signature()
				;
			},
			array('-s', '--signature')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script
					->runTests(false)
					->extractTo($values[0])
				;
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
		
		$this->argumentsParser->addHandler(
	      function($script, $argument, $values) {
				if (sizeof($values) !== 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}
            
				$script
				   ->runTests(false)
				   ->useScript($values[0]);
				return;
			},
			array('-u', '--use')
		);

		return parent::run($arguments);
	}

	public function help(array $options = array())
	{
		return parent::help(array(
				'-i, --infos' => $this->locale->_('Display informations'),
				'-s, --signature' => $this->locale->_('Display phar signature'),
				'-e <dir>, --extract <dir>' => $this->locale->_('Extract all file from phar in <dir>'),
				'--testIt' => $this->locale->_('Execute all Atoum unit tests'),
				'-u <script> <args>, --use <script> <args>' => $this->locale->_('Run the \mageekguy\atoum\scripts\<script> with <args> as arguments')
			)
		);
	}

	public function useScript($script)
	{
	   $scriptName = '\mageekguy\atoum\scripts\\'.$script;
	   if ($this->adapter->class_exists($scriptName) === false)
	   {
	      throw new exceptions\logic('The class \'' . $scriptName. ' doesn\'t exist');
	   }
	   
	   $runScript = new $scriptName(__FILE__);
	   
	   if (is_a($runScript, '\mageekguy\atoum\script') === false)
	   {
	      throw new exceptions\logic('The class \'' . $scriptName. ' is not a \mageekguy\atoum\script instance');
	   }
	   
	   $superglobals = $this->getArgumentsParser()->getSuperglobals();
	   $arguments = array_slice($superglobals->_SERVER['argv'], 1);
	   
		if (sizeof($arguments) > 0)
		{
		   do
		   {
			   $value = array_shift($arguments);
			}
		   while (sizeof($arguments) > 0 && $value != $script);
			
		}
	   array_unshift($arguments, __FILE__);
	   
	   $superglobals->_SERVER = array('argv' => $arguments);
	   $runScript->getArgumentsParser()->setSuperglobals($superglobals);
	   
	   $this->writeLabel($this->locale->_('Running script:'), $script);
	   $runScript->run();
      exit;
	}
	
	public function infos()
	{
		$phar = new \phar(\phar::running());

		$this->writeMessage($this->locale->_('Informations:') . PHP_EOL);
		$this->writeLabels($phar->getMetadata());

		return $this;
	}

	public function signature()
	{
		$phar = new \phar(\phar::running());

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

		$phar = new \phar($this->getName());

		$phar->extractTo($directory);

		return $this;
	}

	public function testIt()
	{
		foreach (new \recursiveIteratorIterator(new atoum\src\iterator\filter(new \recursiveDirectoryIterator(\phar::running() . '/tests/units/classes'))) as $file)
		{
			require_once($file->getPathname());
		}

		return $this;
	}
}

?>
