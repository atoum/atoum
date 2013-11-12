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
