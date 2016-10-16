<?php

namespace mageekguy\atoum\tests\units\scripts\git;

require __DIR__ . '/../../../runner.php';

use
	atoum,
	atoum\scripts,
	atoum\cli\commands,
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
			->string(testedClass::defaultTagFile)->isEqualTo('.tag')
			->string(testedClass::versionPattern)->isEqualTo('$Rev: %s $')
		;
	}

	public function test__construct()
	{
		$this
			->if($pusher = new testedClass(__FILE__))
			->then
				->string($pusher->getRemote())->isEqualTo(testedClass::defaultRemote)
				->string($pusher->getTagFile())->isEqualTo(__DIR__ . DIRECTORY_SEPARATOR . testedClass::defaultTagFile)
				->object($pusher->getTaggerEngine())->isEqualTo(new scripts\tagger\engine())
				->string($pusher->getWorkingDirectory())->isEqualTo(getcwd())
				->object($pusher->getGit())->isEqualTo(new commands\git())
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

	public function testSetGit()
	{
		$this
			->if($pusher = new testedClass(__FILE__))
			->then
				->object($pusher->setGit($git = new commands\git()))->isIdenticalTo($pusher)
				->object($pusher->getGit())->isIdenticalTo($git)
				->object($pusher->setGit())->isIdenticalTo($pusher)
				->object($pusher->getGit())
					->isNotIdenticalTo($git)
					->isEqualTo(new commands\git())
		;
	}

	public function testRun()
	{
		$this
			->given(
				$pusher = new testedClass(__FILE__),
				$pusher->setTaggerEngine($taggerEngine = new \mock\mageekguy\atoum\scripts\tagger\engine()),
				$pusher->setGit($git = new \mock\mageekguy\atoum\cli\commands\git()),
				$pusher->setForceMode(true),
				$pusher->setErrorWriter($errorWriter = new \mock\mageekguy\atoum\writers\std\err()),
				$pusher->setInfoWriter($infoWriter = new \mock\mageekguy\atoum\writers\std\out()),
				$this->calling($infoWriter)->write = $infoWriter
			)

			->assert('Pusher should write error if tag file is not readable')
			->if(
				$this->calling($errorWriter)->write = $errorWriter,
				$this->function->file_get_contents = false
			)
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->mock($errorWriter)->call('write')->withArguments('Unable to read \'' . $pusher->getTagFile() . '\'')->once()

			->assert('Pusher should write error if tag file is not writable')
			->if(
				$this->calling($errorWriter)->write = $errorWriter,
				$this->function->file_put_contents = false,
				$this->function->file_get_contents = '0.0.0'
			)
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->mock($errorWriter)->call('write')->withArguments('Unable to write in \'' . $pusher->getTagFile() . '\'')->once()

			->assert('Pusher should tag code and commit it if tag file is writable')
			->if(
				$this->function->file_put_contents = function($path, $data) { return strlen($data); },
				$this->calling($taggerEngine)->tagVersion->doesNothing(),
				$this->calling($taggerEngine)->tagChangelog->doesNothing(),
				$this->calling($git)->addAllAndCommit = $git,
				$this->calling($git)->checkoutAllFiles = $git,
				$this->calling($git)->createTag = $git,
				$this->calling($git)->push = $git,
				$this->calling($git)->forcePush = $git,
				$this->calling($git)->pushTag = $git,
				$this->calling($git)->resetHardTo = $git,
				$this->calling($git)->deleteLocalTag = $git
			)
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->function('file_put_contents')->wasCalledWithArguments($pusher->getTagFile(), '0.0.1')->once()
				->mock($taggerEngine)
					->call('tagVersion')
						->before($this->mock($taggerEngine)
							->call('tagChangelog')->withArguments('0.0.1')
								->before($this->mock($git)
									->call('addAllAndCommit')->withArguments('Set version to 0.0.1.')
									->before($this->mock($git)
										->call('createTag')->withArguments('0.0.1')
										->before($this->mock($git)
											->call('push')->withArguments($pusher->getRemote())
											->once()
										)
										->before($this->mock($git)
											->call('pushTag')->withArguments('0.0.1', $pusher->getRemote())
											->once()
										)
										->once()
									)
								->once())
							->once())
						->after($this->mock($taggerEngine)
							->call('setSrcDirectory')->withArguments($pusher->getWorkingDirectory())
							->once()
						)
						->after($this->mock($taggerEngine)
							->call('setVersion')->withArguments('$Rev:' . ' 0.0.1 $') // Don't remove concatenation operator to avoid tagger replace the string.
							->once()
						)
						->once()
					->call('tagVersion')
						->before($this->mock($git)
							->call('addAllAndCommit')->withArguments('Set version to dev-master.')->once())
						->after($this->mock($taggerEngine)->call('setSrcDirectory')->withArguments($pusher->getWorkingDirectory())->once())
						->after($this->mock($taggerEngine)->call('setVersion')->withArguments('$Rev:' . ' dev-master $')->once()) // Don't remove concatenation operator to avoid tagger replace the string.
							->once()

			->if($pusher->tagPatchVersion())
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->function('file_put_contents')->wasCalledWithArguments($pusher->getTagFile(), '0.0.1')->twice()

			->if($pusher->tagMinorVersion())
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->function('file_put_contents')->wasCalledWithArguments($pusher->getTagFile(), '0.1.0')->once()

			->if($pusher->tagMajorVersion())
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->function('file_put_contents')->wasCalledWithArguments($pusher->getTagFile(), '1.0.0')->once()

			->assert('Pusher should write error if pushing tag failed and should try to reset repository')
			->if(
				$pusher->tagPatchVersion(),
				$this->calling($git)->pushTag->throw = $exception = new \exception(uniqid())
			)
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->mock($errorWriter)->call('write')->withArguments($exception->getMessage())->once()
				->mock($git)
					->call('resetHardTo')->withArguments('HEAD~2')->once()
					->call('deleteLocalTag')->withArguments('0.0.1')->once()

			->assert('Pusher should write error if pushing commit failed and should try to reset repository')
			->if(
				$this->calling($git)->push->throw = $exception = new \exception(uniqid())
			)
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->mock($errorWriter)->call('write')->withArguments($exception->getMessage())->once()
				->mock($git)
					->call('resetHardTo')->withArguments('HEAD~2')->once()
					->call('deleteLocalTag')->withArguments('0.0.1')->once()

			->assert('Pusher should write error if pushing commit for DEVELOPMENT version failed and should try to reset repository')
			->if(
				$this->calling($git)->push = $git,
				$this->calling($git)->addAllAndCommit[2]->throw = $exception = new \exception(uniqid())
			)
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->mock($errorWriter)->call('write')->withArguments($exception->getMessage())->once()
				->mock($git)
					->call('resetHardTo')->withArguments('HEAD~1')->once()

			->assert('Pusher should write error if pushing commit for DEVELOPMENT version failed and should try to reset repository')
			->if(
				$this->calling($git)->createTag->throw = $exception = new \exception(uniqid())
			)
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->mock($errorWriter)->call('write')->withArguments($exception->getMessage())->once()
				->mock($git)
					->call('resetHardTo')->withArguments('HEAD~1')->once()

			->assert('Pusher should write error if commit failed for STABLE version and should try to reset repository')
			->if(
				$this->calling($git)->createTag = $git,
				$this->calling($git)->addAllAndCommit[1]->throw = $exception = new \exception(uniqid())
			)
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->mock($errorWriter)
					->call('write')->withArguments($exception->getMessage())
						->after($this->mock($git)->call('addAllAndCommit'))
						->once()
				->mock($git)
					->call('checkoutAllFiles')
					->after($this->mock($git)->call('addAllAndCommit'))
					->once()

			->assert('Pusher should write error if reset failed')
			->if(
				$this->calling($git)->checkoutAllFiles->throw = $checkoutAllFilesException = new \exception(uniqid())
			)
			->then
				->object($pusher->run())->isIdenticalTo($pusher)
				->mock($errorWriter)
					->call('write')->withArguments($checkoutAllFilesException->getMessage())
					->once()
		;
	}
}
