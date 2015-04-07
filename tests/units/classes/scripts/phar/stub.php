<?php

namespace mageekguy\atoum\tests\units\scripts\phar;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts\phar,
	mock\mageekguy\atoum as mock
;

require_once __DIR__ . '/../../../runner.php';

class stub extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\scripts\runner');
	}

	public function testClassConstants()
	{
		$this
			->string(phar\stub::scriptsDirectory)->isEqualTo('scripts')
			->string(phar\stub::scriptsExtension)->isEqualTo('.php')
		;
	}

	public function testGetSetPharFactory()
	{
		$this
			->given($this->newTestedInstance(uniqid()))
			->then
				->object($this->testedInstance->getPharFactory())->isInstanceOf('closure')
				->object($this->testedInstance->setPharFactory())->isTestedInstance
				->object($this->testedInstance->getPharFactory())->isInstanceOf('closure')
			->if($factory = function() {})
			->then
				->object($this->testedInstance->setPharFactory($factory))->isTestedInstance
				->object($this->testedInstance->getPharFactory())->isIdenticalTo($factory)
		;
	}

	public function testListScripts()
	{
		$this
			->given(
				$this->newTestedInstance(uniqid()),
				$writer = new \mock\mageekguy\atoum\writer
			)
			->if($this->testedInstance->setOutputWriter($writer))
			->then
				->object($this->testedInstance->listScripts())->isTestedInstance
				->mock($writer)
					->call('write')
						->withArguments('Available scripts are:' . PHP_EOL)->once
						->withArguments('   builder' . PHP_EOL)->once
						->withArguments('   tagger' . PHP_EOL)->once
						->withArguments('   treemap' . PHP_EOL)->once
						->withArguments('   coverage' . PHP_EOL)->once
		;
	}

	public function testInfos()
	{
		$this
			->given(
				$this->newTestedInstance(uniqid()),
				$writer = new \mock\mageekguy\atoum\writer
			)
			->and->mockGenerator->shuntParentClassCalls()
			->and(
				$phar = new \mock\phar(uniqid()),
				$this->calling($phar)->getMetadata = array(
					$key = uniqid() => $value = uniqid(),
					$otherKey = uniqid() => $otherValue = uniqid(),
				),
				$factory = function() use ($phar) { return $phar; }
			)
			->if(
				$this->testedInstance->setHelpWriter($writer),
				$this->testedInstance->setPharFactory($factory)
			)
			->then
				->object($this->testedInstance->infos())->isTestedInstance
				->mock($writer)
					->call('write')
						->withArguments('   ' . $key . ': ' . $value)->once
						->withArguments('   ' . $otherKey . ': ' . $otherValue)->once
		;
	}

	public function testSignature()
	{
		$this
			->given(
				$this->newTestedInstance(uniqid()),
				$writer = new \mock\mageekguy\atoum\writer
			)
			->and->mockGenerator->shuntParentClassCalls()
			->and(
				$phar = new \mock\phar(uniqid()),
				$this->calling($phar)->getSignature = array('hash' => $signature = uniqid()),
				$factory = function() use ($phar) { return $phar; }
			)
			->if(
				$this->testedInstance->setHelpWriter($writer),
				$this->testedInstance->setPharFactory($factory)
			)
			->then
				->object($this->testedInstance->signature())->isTestedInstance
				->mock($writer)
					->call('write')->withArguments('Signature: ' . $signature)->once
		;
	}

	public function testVersion()
	{
		$this
			->given(
				$this->newTestedInstance(uniqid()),
				$writer = new \mock\mageekguy\atoum\writer
			)
			->if($this->testedInstance->setInfoWriter($writer))
			->then
				->object($this->testedInstance->version())->isTestedInstance
				->mock($writer)
					->call('write')->withArguments(sprintf('atoum version %s by %s (%s)', atoum\version, atoum\author, \phar::running()))->once
		;
	}

	public function testUpdate()
	{
		$this
			->if($stub = new phar\stub(uniqid()))
			->and($stub->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->ini_get = function($name) { return $name === 'phar.readonly' ? 1 : ini_get($name); })
			->and($stub->setLocale($locale = new mock\locale()))
			->and($stub->setOutputWriter($outputWriter = new mock\writers\std\out()))
			->and($this->calling($outputWriter)->clear->doesNothing())
			->and($this->calling($outputWriter)->write->doesNothing())
			->and($stub->setInfoWriter($infoWriter = new mock\writers\std\out()))
			->and($this->calling($infoWriter)->write->doesNothing())
			->then
				->exception(function() use ($stub) { $stub->update(); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to update the PHAR, phar.readonly is set, use \'-d phar.readonly=0\'')
			->if($adapter->ini_get = function($name) { return $name === 'phar.readonly' ? 0 : $name = 'allow_url_fopen' ? 0 : ini_get($name); })
			->then
				->exception(function() use ($stub) { $stub->update(); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to update the PHAR, allow_url_fopen is not set, use \'-d allow_url_fopen=1\'')
			->if($adapter->ini_get = function($name) { return $name === 'phar.readonly' ? 0 : $name = 'allow_url_fopen' ? 1 : ini_get($name); })
			->and($stub->setPharFactory(function($path) use (& $phar) {
						$pharController = new atoum\mock\controller();
						$pharController->__construct = function() {};
						$pharController->offsetExists = true;
						$pharController->offsetGet = function($path) { return $path; };
						$pharController->offsetSet = function() {};
						$phar = new \mock\phar($path);

						return $phar;
					}
				)
			)
			->and($adapter->file_get_contents = function($path) use (& $currentVersion) {
					switch ($path)
					{
						case 'versions':
							return serialize(array('1' => $currentVersion = uniqid(), 'current' => '1'));

						case phar\stub::updateUrl:
							return json_encode(array());

						default:
							return false;
					}
				}
			)
			->then
				->object($stub->update())->isIdenticalTo($stub)
				->adapter($adapter)
					->call('file_get_contents')->withArguments(sprintf(phar\stub::updateUrl, json_encode(array($currentVersion))))->once()
				->mock($phar)
					->call('offsetGet')->withArguments('versions')->once()
				->mock($locale)
					->call('_')
						->withArguments('Checking if a new version is available...')->once()
						->withArguments('Checking if a new version is available... Done!' . PHP_EOL)->once()
						->withArguments('There is no new version available!')->once()
				->mock($outputWriter)
					->call('write')->withArguments('Checking if a new version is available...')->once()
					->call('write')
						->after($this->mock($outputWriter)->call('clear')->once())
						->withArguments('Checking if a new version is available... Done!' . PHP_EOL)
							->once()
		;
	}
}
