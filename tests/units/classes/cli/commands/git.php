<?php

namespace mageekguy\atoum\tests\units\cli\commands;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\cli,
	mageekguy\atoum\cli\commands\git as testedClass
;

class git extends atoum
{
	public function testClass()
	{
		$this->string(testedClass::defaultPath)->isEqualTo('git');
	}

	public function test__construct()
	{
		$this
			->if($git = new testedClass())
			->then
				->string($git->getPath())->isEqualTo('git')
				->object($git->getCommand())->isEqualTo(new cli\command(testedClass::defaultPath))
		;
	}

	public function testSetCommand()
	{
		$this
			->if($git = new testedClass())
			->then
				->object($git->setCommand($command = new cli\command()))->isIdenticalTo($git)
				->object($git->getCommand())->isIdenticalTo($command)
				->object($git->setCommand())->isIdenticalTo($git)
				->object($git->getCommand())
					->isNotIdenticalTo($command)
					->isEqualTo(new cli\command())
		;
	}

	public function testAddAndCommitAll()
	{
		$this
			->given(
				$git = new testedClass(),
				$git->setCommand($command = new \mock\mageekguy\atoum\cli\command()),
				$this->calling($command)->run = $command
			)

			->if(
				$this->calling($command)->getExitCode = 0
			)
			->then
				->object($git->addAllAndCommit($message = uniqid()))->isIdenticalTo($git)
				->mock($command)
					->call('reset')
					->before($this->mock($command)
						->call('addOption')->withArguments('commit -am \'' . $message . '\'')
						->before($this->mock($command)
							->call('run')
							->once()
						)
						->once()
					)
					->once()

			->if(
				$this->calling($command)->getExitCode = rand(1, PHP_INT_MAX),
				$this->calling($command)->getStderr = $errorMessage = uniqid()
			)
			->then
				->exception(function() use ($git) { $git->addAllAndCommit(uniqid()); })
					->isInstanceOf('mageekguy\atoum\cli\command\exception')
					->hasMessage('Unable to execute \'' . $command . '\': ' . $errorMessage)
		;
	}

	public function testResetHard()
	{
		$this
			->given(
				$git = new testedClass(),
				$git->setCommand($command = new \mock\mageekguy\atoum\cli\command()),
				$this->calling($command)->run = $command
			)

			->if(
				$this->calling($command)->getExitCode = 0
			)
			->then
				->object($git->resetHardTo($commit = uniqid()))->isIdenticalTo($git)
				->mock($command)
					->call('reset')
					->before($this->mock($command)
						->call('addOption')->withArguments('reset --hard ' . $commit)
						->before($this->mock($command)
							->call('run')
							->once()
						)
						->once()
					)
					->once()

			->if(
				$this->calling($command)->getExitCode = rand(1, PHP_INT_MAX),
				$this->calling($command)->getStderr = $errorMessage = uniqid()
			)
			->then
				->exception(function() use ($git) { $git->resetHardTo(uniqid()); })
					->isInstanceOf('mageekguy\atoum\cli\command\exception')
					->hasMessage('Unable to execute \'' . $command . '\': ' . $errorMessage)
		;
	}

	public function testCreateTag()
	{
		$this
			->given(
				$git = new testedClass(),
				$git->setCommand($command = new \mock\mageekguy\atoum\cli\command()),
				$this->calling($command)->run = $command
			)

			->if(
				$this->calling($command)->getExitCode = 0
			)
			->then
				->object($git->createTag($tag = uniqid()))->isIdenticalTo($git)
				->mock($command)
					->call('reset')
					->before($this->mock($command)
						->call('addOption')->withArguments('tag ' . $tag)
						->before($this->mock($command)
							->call('run')
							->once()
						)
						->once()
					)
					->once()

			->if(
				$this->calling($command)->getExitCode = rand(1, PHP_INT_MAX),
				$this->calling($command)->getStderr = $errorMessage = uniqid()
			)
			->then
				->exception(function() use ($git) { $git->createTag(uniqid()); })
					->isInstanceOf('mageekguy\atoum\cli\command\exception')
					->hasMessage('Unable to execute \'' . $command . '\': ' . $errorMessage)
		;
	}

	public function testDeleteLocalTag()
	{
		$this
			->given(
				$git = new testedClass(),
				$git->setCommand($command = new \mock\mageekguy\atoum\cli\command()),
				$this->calling($command)->run = $command
			)

			->if(
				$this->calling($command)->getExitCode = 0
			)
			->then
				->object($git->deleteLocalTag($tag = uniqid()))->isIdenticalTo($git)
				->mock($command)
					->call('reset')
					->before($this->mock($command)
						->call('addOption')->withArguments('tag -d ' . $tag)
						->before($this->mock($command)
							->call('run')
							->once()
						)
						->once()
					)
					->once()

			->if(
				$this->calling($command)->getExitCode = rand(1, PHP_INT_MAX),
				$this->calling($command)->getStderr = $errorMessage = uniqid()
			)
			->then
				->exception(function() use ($git) { $git->deleteLocalTag(uniqid()); })
					->isInstanceOf('mageekguy\atoum\cli\command\exception')
					->hasMessage('Unable to execute \'' . $command . '\': ' . $errorMessage)
		;
	}

	public function testPush()
	{
		$this
			->given(
				$git = new testedClass(),
				$git->setCommand($command = new \mock\mageekguy\atoum\cli\command()),
				$this->calling($command)->run = $command
			)

			->if(
				$this->calling($command)->getExitCode = 0
			)
			->then
				->object($git->push())->isIdenticalTo($git)
				->mock($command)
					->call('reset')
					->before(
						$this->mock($command)
							->call('addOption')->withArguments('push origin master')
							->before(
								$this->mock($command)
									->call('run')->twice()
									->after($this->mock($command)->call('addOption')->withArguments('rev-parse --abbrev-ref HEAD'))
							)
							->once()
					)
					->thrice()

				->object($git->push($remote = uniqid()))->isIdenticalTo($git)
				->mock($command)
					->call('reset')
					->before($this->mock($command)
						->call('addOption')->withArguments('push ' . $remote . ' master')
						->before($this->mock($command)
							->call('run')
							->exactly(4)
						)
						->once()
					)
					->exactly(6)

				->object($git->push($remote = uniqid(), $branch = uniqid()))->isIdenticalTo($git)
				->mock($command)
					->call('reset')
					->before($this->mock($command)
							->call('addOption')->withArguments('push ' . $remote . ' ' . $branch)
							->before($this->mock($command)
								->call('run')
								->exactly(5)
							)
							->once()
					)
					->exactly(7)

			->if(
				$this->calling($command)->getExitCode = rand(1, PHP_INT_MAX),
				$this->calling($command)->getStderr = $errorMessage = uniqid()
			)
			->then
				->exception(function() use ($git) { $git->push(); })
					->isInstanceOf('mageekguy\atoum\cli\command\exception')
					->hasMessage('Unable to execute \'' . $command . '\': ' . $errorMessage)
		;
	}

	public function testForcePush()
	{
		$this
			->given(
				$git = new testedClass(),
				$git->setCommand($command = new \mock\mageekguy\atoum\cli\command()),
				$this->calling($command)->run = $command
			)

			->if(
				$this->calling($command)->getExitCode = 0
			)
			->then
				->object($git->forcePush())->isIdenticalTo($git)
				->mock($command)
					->call('reset')
					->before(
						$this->mock($command)
							->call('addOption')->withArguments('push --force origin master')
							->before(
								$this->mock($command)
									->call('run')->twice()
									->after($this->mock($command)->call('addOption')->withArguments('rev-parse --abbrev-ref HEAD'))
							)
							->once()
					)
					->thrice()

				->object($git->forcePush($remote = uniqid()))->isIdenticalTo($git)
				->mock($command)
					->call('reset')
					->before($this->mock($command)
						->call('addOption')->withArguments('push --force ' . $remote . ' master')
						->before($this->mock($command)
							->call('run')
							->exactly(4)
						)
						->once()
					)
					->exactly(6)

				->object($git->forcePush($remote = uniqid(), $branch = uniqid()))->isIdenticalTo($git)
				->mock($command)
					->call('reset')
					->before($this->mock($command)
							->call('addOption')->withArguments('push --force ' . $remote . ' ' . $branch)
							->before($this->mock($command)
								->call('run')
								->exactly(5)
							)
							->once()
					)
					->exactly(7)

			->if(
				$this->calling($command)->getExitCode = rand(1, PHP_INT_MAX),
				$this->calling($command)->getStderr = $errorMessage = uniqid()
			)
			->then
				->exception(function() use ($git) { $git->forcePush(); })
					->isInstanceOf('mageekguy\atoum\cli\command\exception')
					->hasMessage('Unable to execute \'' . $command . '\': ' . $errorMessage)
		;
	}

	public function testPushTag()
	{
		$this
			->given(
				$git = new testedClass(),
				$git->setCommand($command = new \mock\mageekguy\atoum\cli\command()),
				$this->calling($command)->run = $command
			)

			->if(
				$this->calling($command)->getExitCode = 0
			)
			->then
				->object($git->pushTag($tag = uniqid()))->isIdenticalTo($git)
				->mock($command)
					->call('reset')
					->before($this->mock($command)
						->call('addOption')->withArguments('push origin ' . $tag)
						->before($this->mock($command)
							->call('run')
							->once()
						)
						->once()
					)
					->once()

				->object($git->pushTag($tag = uniqid(), $remote = uniqid()))->isIdenticalTo($git)
				->mock($command)
					->call('reset')
					->before($this->mock($command)
						->call('addOption')->withArguments('push ' . $remote . ' ' . $tag)
						->before($this->mock($command)
							->call('run')
							->twice()
						)
						->once()
					)
					->twice()

			->if(
				$this->calling($command)->getExitCode = rand(1, PHP_INT_MAX),
				$this->calling($command)->getStderr = $errorMessage = uniqid()
			)
			->then
				->exception(function() use ($git) { $git->pushTag(uniqid()); })
					->isInstanceOf('mageekguy\atoum\cli\command\exception')
					->hasMessage('Unable to execute \'' . $command . '\': ' . $errorMessage)
		;
	}

	public function testCheckoutAllFiles()
	{
		$this
			->given(
				$git = new testedClass(),
				$git->setCommand($command = new \mock\mageekguy\atoum\cli\command()),
				$this->calling($command)->run = $command
			)

			->if(
				$this->calling($command)->getExitCode = 0
			)
			->then
				->object($git->checkoutAllFiles())->isIdenticalTo($git)
				->mock($command)
					->call('reset')
					->before($this->mock($command)
						->call('addOption')->withArguments('checkout .')
						->before($this->mock($command)
							->call('run')
							->once()
						)
						->once()
					)
					->once()

			->if(
				$this->calling($command)->getExitCode = rand(1, PHP_INT_MAX),
				$this->calling($command)->getStderr = $errorMessage = uniqid()
			)
			->then
				->exception(function() use ($git) { $git->checkoutAllFiles(); })
					->isInstanceOf('mageekguy\atoum\cli\command\exception')
					->hasMessage('Unable to execute \'' . $command . '\': ' . $errorMessage)
		;
	}
}
