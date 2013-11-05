<?php

namespace mageekguy\atoum\scripts\git;

use
	mageekguy\atoum,
	mageekguy\atoum\cli,
	mageekguy\atoum\script,
	mageekguy\atoum\scripts
;

class pusher extends script\configurable
{
	const defaultRemote = 'origin';
	const defaultBranch = 'master';
	const defaultTagFile = '.tag';

	protected $remote = '';
	protected $branch = '';
	protected $tagFile = null;
	protected $workingDirectory = '';

	public function __construct($name, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $adapter);

		$this
			->setRemote()
			->setBranch()
			->setTagFile()
			->setTaggerEngine()
			->setWorkingDirectory()
		;
	}

	public function setRemote($remote = null)
	{
		$this->remote = $remote ?: self::defaultRemote;

		return $this;
	}

	public function getRemote()
	{
		return $this->remote;
	}

	public function setBranch($branch = null)
	{
		$this->branch = $branch ?: self::defaultBranch;

		return $this;
	}

	public function getBranch()
	{
		return $this->branch;
	}

	public function setTagFile($tagFile = null)
	{
		if ($tagFile !== null)
		{
			$tagFile = (string) $tagFile;
		}
		else
		{
			$tagFile = $this->getDirectory() . DIRECTORY_SEPARATOR . self::defaultTagFile;
		}

		$this->tagFile = $tagFile;

		return $this;
	}

	public function getTagFile()
	{
		return $this->tagFile;
	}

	public function setTaggerEngine(scripts\tagger\engine $engine = null)
	{
		$this->taggerEngine = $engine ?: new scripts\tagger\engine();

		return $this;
	}

	public function getTaggerEngine()
	{
		return $this->taggerEngine;
	}

	public function setWorkingDirectory($workingDirectory = null)
	{
		$this->workingDirectory = $workingDirectory ?: $this->adapter->getcwd();

		return $this;
	}

	public function getWorkingDirectory()
	{
		return $this->workingDirectory;
	}

	protected function setArgumentHandlers()
	{
		parent::setArgumentHandlers()
			->addArgumentHandler(
				function($script, $argument, $remote) {
					if (sizeof($remote) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $remote, $script->getName()));
					}

					$script->setRemote(reset($remote));
				},
				array('-tr', '--to-remote'),
				'<string>',
				$this->locale->_('<string> will be used as remote')
			)
			->addArgumentHandler(
				function($script, $argument, $branch) {
					if (sizeof($branch) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setBranch(reset($branch));
				},
				array('-ib', '--in-branch'),
				'<string>',
				$this->locale->_('<string> will be used as remote branch')
			)
			->addArgumentHandler(
				function($script, $argument, $tagFile) {
					if (sizeof($tagFile) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setTagFile(reset($tagFile));
				},
				array('-ib', '--in-branch'),
				'<path>',
				$this->locale->_('File <path> will be used to store last tag')
			)
		;

		return $this;
	}

	protected function doRun()
	{
		$tag = trim(@file_get_contents($this->tagFile)) ?: 0;

		file_put_contents($this->tagFile, ++$tag);

		$tag = '0.0.' . $tag;

		$this->taggerEngine
			->setSrcDirectory($this->workingDirectory)
			->setVersion('$Rev: ' . $tag . ' $')
			->tagVersion()
		;

		$git = new cli\command('git');

		$git
			->addOption('commit')
			->addOption('-am', '\'Set version to ' . $tag .'.\'')
			->run()
		;

		if ($git->getExitCode() !== 0)
		{
			throw new cli\command\exception('Unable to commit \'' . $this->tagFile . '\' in repository: ' . $git->getStderr());
		}

		$git
			->reset()
			->addOption('tag', $tag)
			->run()
		;

		if ($git->getExitCode() !== 0)
		{
			throw new cli\command\exception('Unable to apply tag \'' . $tag  . '\': ' . $git->getStderr());
		}

		$git
			->reset()
			->addOption('push')
			->addOption('--tags')
			->addOption($this->remote)
			->addOption($this->branch)
			->run()
		;

		if ($git->getExitCode() !== 0)
		{
			throw new cli\command\exception('Unable to push tag \'' . $tag  . '\' to \'' . $this->remote . '\' in branch \'' . $this->branch . '\': ' . $git->getStderr());
		}

		$tag = 'DEVELOPMENT-' . $tag;

		$this->taggerEngine
			->setVersion('$Rev: ' . $tag . ' $')
			->tagVersion()
		;

		$git
			->reset()
			->addOption('commit')
			->addOption('-am', '\'Set version to ' . $tag .'.\'')
			->run()
		;

		if ($git->getExitCode() !== 0)
		{
			throw new cli\command\exception('Unable to commit tag \'' . $tag  . '\': ' . $git->getStderr());
		}

		$git
			->reset()
			->addOption('push')
			->addOption($this->remote)
			->addOption($this->branch)
			->run()
		;

		if ($git->getExitCode() !== 0)
		{
			throw new cli\command\exception('Unable to push tag \'' . $tag  . '\' to \'' . $this->remote . '\' in branch \'' . $this->branch . '\': ' . $git->getStderr());
		}

		return $this;
	}
}
