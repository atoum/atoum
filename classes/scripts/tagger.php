<?php

namespace mageekguy\atoum\scripts;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class tagger extends atoum\script
{
	protected $tagger = null;
	protected $tagVersion = true;

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $locale, $adapter);

		$this->setTagger(new atoum\tagger($this->adapter));
	}

	public function setTagger(atoum\tagger $tagger)
	{
		$this->tagger = $tagger;

		return $this;
	}

	public function getTagger()
	{
		return $this->tagger;
	}

	public function run(array $arguments = array())
	{
		$tagger = $this->tagger;

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
			function($script, $argument, $directory) use ($tagger) {
				if (sizeof($directory) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$tagger->setDestinationDirectory(current($directory));
			},
			array('-d', '--destination-directory')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $directory) use ($tagger) {
				if (sizeof($directory) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$tagger->setSrcDirectory(current($directory));
			},
			array('-s', '--src-directory')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $versionPattern) use ($tagger) {
				if (sizeof($versionPattern) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$tagger->setVersionPattern(current($versionPattern));
			},
			array('-vp', '--version-pattern')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $version) use ($tagger) {
				if (sizeof($version) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$tagger->setVersion(current($version));
			},
			array('-v', '--version')
		);

		parent::run($arguments);

		if ($this->tagVersion === true)
		{
			$tagger->tagVersion();
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
