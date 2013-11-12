<?php

namespace mageekguy\atoum\tests\units\scripts\git;

require __DIR__ . '/../../../runner.php';

use
	atoum,
	atoum\cli,
	atoum\scripts,
	atoum\scripts\git\pusher as testedClass
;

class pusher extends atoum
{
	public function testClass()
	{
		$this->testedClass->extends('atoum\script\configurable');
	}

	public function testClassConstants()
	{
		$this
			->string(testedClass::defaultRemote)->isEqualTo('origin')
			->string(testedClass::defaultBranch)->isEqualTo('master')
			->string(testedClass::defaultTagFile)->isEqualTo('.tag')
			->string(testedClass::defaultGitPath)->isEqualTo('git')
		;
	}

	public function test__construct()
	{
		$this
			->if($pusher = new testedClass(__FILE__))
			->then
				->string($pusher->getRemote())->isEqualTo(testedClass::defaultRemote)
				->string($pusher->getBranch())->isEqualTo(testedClass::defaultBranch)
				->string($pusher->getTagFile())->isEqualTo(__DIR__ . DIRECTORY_SEPARATOR . testedClass::defaultTagFile)
				->object($pusher->getTaggerEngine())->isEqualTo(new scripts\tagger\engine())
				->string($pusher->getWorkingDirectory())->isEqualTo(getcwd())
				->object($pusher->getCliCommand())->isEqualTo(new cli\command())
		;
	}

	public function testSetRemote()
	{
		$this
			->if($pusher = new testedClass(__FILE__))
			->then
				->object($pusher->setRemote($remote = uniqid()))->isIdenticalTo($pusher)
				->string($pusher->getRemote())->isEqualTo($remote)
				->object($pusher->setRemote())->isIdenticalTo($pusher)
				->string($pusher->getRemote())->isEqualTo(testedClass::defaultRemote)
		;
	}

	public function testSetBranch()
	{
		$this
			->if($pusher = new testedClass(__FILE__))
			->then
				->object($pusher->setBranch($branch = uniqid()))->isIdenticalTo($pusher)
				->string($pusher->getBranch())->isEqualTo($branch)
				->object($pusher->setBranch())->isIdenticalTo($pusher)
				->string($pusher->getBranch())->isEqualTo(testedClass::defaultBranch)
		;
	}

	public function testSetTagFile()
	{
		$this
			->if($pusher = new testedClass(__FILE__))
			->then
				->object($pusher->setTagFile($tagFile = uniqid()))->isIdenticalTo($pusher)
				->string($pusher->getTagFile())->isEqualTo($tagFile)
				->object($pusher->setTagFile())->isIdenticalTo($pusher)
				->string($pusher->getTagFile())->isEqualTo(__DIR__ . DIRECTORY_SEPARATOR . testedClass::defaultTagFile)
		;
	}

	public function testSetTaggerEngine()
	{
		$this
			->if($pusher = new testedClass(__FILE__))
			->then
				->object($pusher->setTaggerEngine($taggerEngine = new scripts\tagger\engine()))->isIdenticalTo($pusher)
				->object($pusher->getTaggerEngine())->isIdenticalTo($taggerEngine)
				->object($pusher->setTaggerEngine())->isIdenticalTo($pusher)
				->object($pusher->getTaggerEngine())
					->isNotIdenticalTo($taggerEngine)
					->isEqualTo(new scripts\tagger\engine())
		;
	}

	public function testSetWorkingDirectory()
	{
		$this
			->if($pusher = new testedClass(__FILE__))
			->then
				->object($pusher->setWorkingDirectory($workingDirectory = uniqid()))->isIdenticalTo($pusher)
				->string($pusher->getWorkingDirectory())->isEqualTo($workingDirectory)
				->object($pusher->setWorkingDirectory())->isIdenticalTo($pusher)
				->string($pusher->getWorkingDirectory())->isEqualTo(getcwd())
		;
	}

	public function testSetCliCommand()
	{
		$this
			->if($pusher = new testedClass(__FILE__))
			->then
				->object($pusher->setCliCommand($cliCommand = new cli\command()))->isIdenticalTo($pusher)
				->object($pusher->getCliCommand())->isIdenticalTo($cliCommand)
				->object($pusher->setCliCommand())->isIdenticalTo($pusher)
				->object($pusher->getCliCommand())
					->isNotIdenticalTo($cliCommand)
					->isEqualTo(new cli\command())
		;
	}

	public function testSetGitPath()
	{
		$this
			->if($pusher = new testedClass(__FILE__))
			->then
				->object($pusher->setGitPath($gitPath = uniqid()))->isIdenticalTo($pusher)
				->string($pusher->getGitPath())->isEqualTo($gitPath)
				->object($pusher->setGitPath())->isIdenticalTo($pusher)
				->string($pusher->getGitPath())->isEqualTo(testedClass::defaultGitPath)
		;
	}

	public function testRun()
	{
		$this
			->given(
				$pusher = new testedClass(__FILE__),
				$pusher->setTaggerEngine($taggerEngine = new \mock\mageekguy\atoum\scripts\tagger\engine()),
				$pusher->setCliCommand($git = new \mock\mageekguy\atoum\cli\command()),
				$pusher->setErrorWriter($errorWriter = new \mock\mageekguy\atoum\writers\std\err())
			)

			->assert('Should write error on stderr if tag file is not writable')
			->if(
				$this->calling($errorWriter)->write = $errorWriter,
				$this->function->file_put_contents = false,
				$this->function->file_get_contents = false
			)
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->mock($errorWriter)->call('write')->withArguments('Error: Unable to write in \'' . $pusher->getTagFile() . '\'' . PHP_EOL)->once()
				->mock($git)->call('run')->never()

			->assert('Should tag code and commit if tag file is writable')
			->if(
				$this->function->file_put_contents = function($path, $data) { return strlen($data); },
				$this->calling($taggerEngine)->tagVersion->doesNothing(),
				$this->calling($git)->getExitCode = 0,
				$this->calling($git)->run->doesNothing()
			)
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->function('file_put_contents')->wasCalledWithArguments($pusher->getTagFile(), 1)->once()
				->mock($taggerEngine)
					->call('tagVersion')
						->after($this->mock($taggerEngine)->call('setSrcDirectory')->withArguments($pusher->getWorkingDirectory())->once())
						->after($this->mock($taggerEngine)->call('setVersion')->withArguments('$Rev: 0.0.1 $')->once())
							->once()
				->mock($git)
					->call('run')
						->after(
							$this->mock($git)->call('setBinaryPath')->withArguments($pusher->getGitPath())->once(),
							$this->mock($git)->call('addOption')->withArguments('commit -am \'Set version to 0.0.1.\'')->once()
						)->once()

			->assert('Should write error on stderr if commit failed')
			->if(
				$this->calling($git)->getExitCode = rand(1, PHP_INT_MAX),
				$this->calling($git)->getStderr = $errorMessage = uniqid()
			)
			->then
				->exception(function() use ($pusher) { $pusher->run(); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to execute ' . $git . ': ' . $errorMessage)
				->mock($git)
					->call('run')
						->after(
							$this->mock($git)->call('getExitCode')->once(),
							$this->mock($git)->call('addOption')->withArguments('reset --hard HEAD~1')->once()
						)->once()

				/*
			->assert('Should throw an exception if reset failed')
			->if(
				$this->calling($git)->getExitCode = rand(1, PHP_INT_MAX),
				$this->calling($git)->getStderr = $errorMessage = uniqid()
			)
			->then
				->exception(function() use ($pusher) { $pusher->run(); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to execute ' . $git . ': ' . $errorMessage)
				->mock($git)
					->call('run')
						->after(
							$this->mock($git)->call('getExitCode')->once(),
							$this->mock($git)->call('addOption')->withArguments('reset --hard HEAD~1')->once()
						)->once()


				$this->calling($git)->getExitCode = 0,
				$this->calling($git)->run->doesNothing(),

				->object($pusher->run())->isIdenticalTo($pusher)
				->mock($taggerEngine)
					->call('tagVersion')
						->after($this->mock($taggerEngine)->call('setSrcDirectory')->withArguments($pusher->getWorkingDirectory())->once())
						->after($this->mock($taggerEngine)->call('setVersion')->withArguments('$Rev: 0.0.' . ($tag + 1) . ' $')->once())
						->before($this->mock($taggerEngine)->call('setVersion')->withArguments('$Rev: DEVELOPMENT-0.0.' . ($tag + 1) . ' $')->once())
							->once()
					->call('tagVersion')
						->after($this->mock($taggerEngine)->call('setVersion')->withArguments('$Rev: DEVELOPMENT-0.0.' . ($tag + 1) . ' $')->once())
							->once()
				*/
		;
	}
}
