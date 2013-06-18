<?php

namespace mageekguy\atoum\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\scripts\tagger
;

class tagger extends atoum\script
{
	protected $engine = null;

	public function __construct($name, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $adapter);

		$this->setEngine();
	}

	public function setEngine(tagger\engine $engine = null)
	{
		$this->engine = $engine ?: new scripts\tagger\engine();

		$this->setArgumentHandlers();

		return $this;
	}

	public function getEngine()
	{
		return $this->engine;
	}

	protected function setArgumentHandlers()
	{
		if ($this->engine !== null)
		{
			$engine = $this->engine;

			$this
				->addArgumentHandler(
					function($script, $argument, $values) {
						if (sizeof($values) != 0)
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
					function($script, $argument, $version) use ($engine) {
						if (sizeof($version) != 1)
						{
							throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
						}

						$engine->setVersion(current($version));
					},
					array('-v', '--version'),
					'<string>',
					$this->locale->_('Use <string> as version value')
				)
				->addArgumentHandler(
					function($script, $argument, $versionPattern) use ($engine) {
						if (sizeof($versionPattern) != 1)
						{
							throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
						}

						$engine->setVersionPattern(current($versionPattern));
					},
					array('-vp', '--version-pattern'),
					'<regex>',
					$this->locale->_('Use <regex> to set version in source files')
				)
				->addArgumentHandler(
					function($script, $argument, $directory) use ($engine) {
						if (sizeof($directory) != 1)
						{
							throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
						}

						$engine->setSrcDirectory(current($directory));
					},
					array('-s', '--src-directory'),
					'<directory>',
					$this->locale->_('Use <directory> as source directory')
				)
				->addArgumentHandler(
					function($script, $argument, $directory) use ($engine) {
						if (sizeof($directory) != 1)
						{
							throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
						}

						$engine->setDestinationDirectory(current($directory));
					},
					array('-d', '--destination-directory'),
					'<directory>',
					 $this->locale->_('Save tagged files in <directory>')
				)
			;
		}

		return $this;
	}

	protected function doRun()
	{
		$this->engine->tagVersion();

		return $this;
	}
}
