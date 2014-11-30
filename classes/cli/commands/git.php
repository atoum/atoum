<?php

namespace mageekguy\atoum\cli\commands;

use
	mageekguy\atoum\cli
;

class git
{
	const defaultPath = 'git';

	protected $command = null;

	public function __construct($path = null)
	{
		$this
			->setCommand()
			->setPath($path)
		;
	}

	public function setPath($path = null)
	{
		$this->command->setBinaryPath($path ?: static::defaultPath);

		return $this;
	}

	public function getPath()
	{
		return $this->command->getBinaryPath();
	}

	public function setCommand(cli\command $command = null)
	{
		$this->command = $command ?: new cli\command();

		return $this;
	}

	public function getCommand()
	{
		return $this->command;
	}

	public function addAllAndCommit($message)
	{
		$this->command
			->reset()
			->addOption('commit -am \'' . addslashes($message) . '\'')
		;

		return $this->run();
	}

	public function resetHardTo($commit)
	{
		$this->command
			->reset()
			->addOption('reset --hard ' . $commit)
		;

		return $this->run();
	}

	public function createTag($tag)
	{
		$this->command
			->reset()
			->addOption('tag ' . $tag)
		;

		return $this->run();
	}

	public function deleteLocalTag($tag)
	{
		$this->command
			->reset()
			->addOption('tag -d ' . $tag)
		;

		return $this->run();
	}

	public function push($remote = null, $branch = null)
	{
		$this->command
			->reset()
			->addOption('push ' . ($remote ?: 'origin') . ' ' . ($branch ?: $this->getCurrentBranch()))
		;

		return $this->run();
	}

	public function forcePush($remote = null, $branch = null)
	{
		$this->command
			->reset()
			->addOption('push --force ' . ($remote ?: 'origin') . ' ' . ($branch ?: $this->getCurrentBranch()))
		;

		return $this->run();
	}

	public function pushTag($tag, $remote = null)
	{
		$this->command
			->reset()
			->addOption('push ' . ($remote ?: 'origin') . ' ' . $tag)
		;

		return $this->run();
	}

	public function checkoutAllFiles()
	{
		$this->command
			->reset()
			->addOption('checkout .')
		;

		return $this->run();
	}

	protected function run()
	{
		if ($this->command->run()->getExitCode() !== 0)
		{
			throw new cli\command\exception('Unable to execute \'' . $this->command . '\': ' . $this->command->getStderr());
		}

		return $this;
	}

	public function getCurrentBranch()
	{
		$this->command
			->reset()
			->addOption('rev-parse --abbrev-ref HEAD')
		;

		$branch = trim($this->run()->command->getStdout()) ?: 'master';

		$this->command->reset();

		return $branch;
	}
}
