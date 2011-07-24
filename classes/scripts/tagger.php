<?php

namespace mageekguy\atoum\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\scripts\tagger
;

class tagger extends atoum\script
{
	protected $engine = null;
	protected $tagVersion = true;

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $locale, $adapter);

		$this->setEngine(new tagger\engine($this->adapter));
	}

	public function setEngine(tagger\engine $engine)
	{
		$this->engine = $engine;

		return $this;
	}

	public function getEngine()
	{
		return $this->engine;
	}

	public function run(array $arguments = array())
	{
		$engine = $this->engine;

		$this->argumentsParser->addHandler(
			function($script, $argument, $values) {
				if (sizeof($values) != 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->help();
			},
			array('-h', '--help')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $directory) use ($engine) {
				if (sizeof($directory) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$engine->setDestinationDirectory(current($directory));
			},
			array('-d', '--destination-directory')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $directory) use ($engine) {
				if (sizeof($directory) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$engine->setSrcDirectory(current($directory));
			},
			array('-s', '--src-directory')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $versionPattern) use ($engine) {
				if (sizeof($versionPattern) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$engine->setVersionPattern(current($versionPattern));
			},
			array('-vp', '--version-pattern')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $version) use ($engine) {
				if (sizeof($version) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$engine->setVersion(current($version));
			},
			array('-v', '--version')
		);

		parent::run($arguments);

		if ($this->tagVersion === true)
		{
			$engine->tagVersion();
		}

		return $this;
	}

	public function help()
	{
		$this
			->writeMessage(sprintf($this->locale->_('Usage: %s [options]'), $this->getName()) . PHP_EOL)
			->writeMessage($this->locale->_('Available options are:') . PHP_EOL)
		;

		$this->writeLabels(
			array(
				'-h, --help' => $this->locale->_('Display this help'),
				'-v <string>, --version <string>' => $this->locale->_('Use <string> as version value'),
				'-vp <regex>, --version-pattern <regex>' => $this->locale->_('Use <regex> to set version in source files'),
				'-s <directory>, --src-directory <directory>' => $this->locale->_('Use <directory> as source directory'),
				'-d <directory>, --destination-directory <directory>' => $this->locale->_('Save tagged files in <directory>'),
			)
		);

		$this->tagVersion = false;

		return $this;
	}
}

?>
