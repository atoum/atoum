<?php

namespace mageekguy\atoum\scripts\git;

use
	mageekguy\atoum,
	mageekguy\atoum\script,
	mageekguy\atoum\scripts,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\cli\commands
;

class pusher extends script\configurable
{
	const defaultRemote = 'origin';
	const defaultBranch = 'master';
	const defaultTagFile = '.tag';
	const defaultGitPath = 'git';
	const versionPattern = '$Rev: %s $';

	protected $remote = '';
	protected $tagFile = null;
	protected $workingDirectory = '';
	protected $taggerEngine = null;
	protected $git = null;

	public function __construct($name, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $adapter);

		$this
			->setRemote()
			->setTagFile()
			->setTaggerEngine()
			->setWorkingDirectory()
			->setGit()
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

	public function setGit(commands\git $git = null)
	{
		$this->git = $git ?: new commands\git();

		return $this;
	}

	public function getGit()
	{
		return $this->git;
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
				function($script, $argument, $tagFile) {
					if (sizeof($tagFile) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setTagFile(reset($tagFile));
				},
				array('-tf', '--tag-file'),
				'<path>',
				$this->locale->_('File <path> will be used to store last tag')
			)
		;

		return $this;
	}

	protected function doRun()
	{
		try
		{
			$tag = @file_get_contents($this->tagFile) ?: 0;
			$tag = trim($tag);

			if (@file_put_contents($this->tagFile, ++$tag) === false)
			{
				throw new exceptions\runtime('Unable to write in \'' . $this->tagFile . '\'');
			}

			$tag = '0.0.' . $tag;

			$this->taggerEngine
				->setSrcDirectory($this->workingDirectory)
				->setVersion(sprintf(static::versionPattern, $tag))
				->tagVersion()
			;

			try
			{
				$this->git
					->addAllAndCommit('Set version to ' . $tag . '.')
					->createTag($tag)
				;
			}
			catch (\exception $exception)
			{
				$this
					->writeError($exception->getMessage())
					->git->resetHardTo('HEAD~1')
				;

				return $this;
			}

			$devTag = 'DEVELOPMENT-' . $tag;

			$this->taggerEngine
				->setVersion(sprintf(static::versionPattern, $devTag))
				->tagVersion()
			;

			try
			{
				$this->git
					->addAllAndCommit('Set version to ' . $devTag . '.')
					->push($this->remote)
					->pushTag($tag, $this->remote)
				;
			}
			catch (\exception $exception)
			{
				$this
					->writeError($exception->getMessage())
					->git
						->resetHardTo('HEAD~2')
						->deleteLocalTag($tag)
				;

				return $this;
			}
		}
		catch (\exception $exception)
		{
			$this->writeError($exception->getMessage());
		}

		return $this;
	}
}
