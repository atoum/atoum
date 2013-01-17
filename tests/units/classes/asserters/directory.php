<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\mock,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class directory extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\directory($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getCall())->isNull()
				->variable($asserter->getAdapter())->isNull()
				->array($asserter->getBeforeMethodCalls())->isEmpty()
				->array($asserter->getBeforeFunctionCalls())->isEmpty()
				->array($asserter->getAfterMethodCalls())->isEmpty()
				->array($asserter->getAfterFunctionCalls())->isEmpty()
		;
	}

	public function testSetWith()
	{
		$this
			->if($directory = null)
			->mockFilesystem()
				->directory()->create($directory)
			->create()
			->and($asserter = new asserters\directory($generator = new asserter\generator()))
			->then
				->object($asserter->setWith((string) $directory))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $value) {
						$asserter->setWith($value = uniqid());
					})
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s is not a directory'), $asserter->getTypeOf($value)))
			->if($directory = new \mock\mageekguy\atoum\mock\filesystem\directory())
			->and($adapter = new test\adapter())
			->and($directory->getMockController()->getStream = $adapter)
			->then
				->object($asserter->setWith($directory))->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testCall()
	{
		$this
			->if($asserter = new \mock\mageekguy\atoum\asserters\directory(new asserter\generator()))
			->and($asserter->getMockController()->atLeastOnce = function() {})
			->then
				->exception(function() use ($asserter) {
						$asserter->call(uniqid());
					})
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Adapter is undefined')
			->if($directory = new \mock\mageekguy\atoum\mock\filesystem\directory())
			->and($adapter = new test\adapter())
			->and($directory->getMockController()->getStream = $adapter)
			->and($asserter->setWith($directory))
			->then
				->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function))
			->if($asserter->withArguments())
			->then
				->object($asserter->getCall())->isEqualTo(new php\call($function, array()))
				->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function))
		;
	}

	public function testHasBeenChecked()
	{
		$this
			->if($generator = new \mock\mageekguy\atoum\asserter\generator())
			->and($asserter = new \mock\mageekguy\atoum\asserters\directory($generator))
			->and($directory = new \mock\mageekguy\atoum\mock\filesystem\directory())
			->and($locale = new \mock\mageekguy\atoum\locale())
			->and($adapter = new test\adapter())
			->and($generator->getMockController()->getLocale = $locale)
			->and($asserter->getMockController()->call = $asserter)
			->and($asserter->getMockController()->atLeastOnce = $asserter)
			->and($directory->getMockController()->getStream = $adapter)
			->and($locale->getMockController()->_ = $message = uniqid())
			->and($asserter->setWith($directory))
			->then
				->object($asserter->hasBeenChecked())->isIdenticalTo($asserter)
				->mock($locale)
					->call('_')->withArguments('directory %s has not been checked')
				->mock($asserter)
					->call('call')->withArguments('url_stat')->once()
					->call('atLeastOnce')->withArguments($message)->once()
		;
	}

	public function testHasNotBeenChecked()
	{
		$this
			->if($generator = new \mock\mageekguy\atoum\asserter\generator())
			->and($asserter = new \mock\mageekguy\atoum\asserters\directory($generator))
			->and($directory = new \mock\mageekguy\atoum\mock\filesystem\directory())
			->and($locale = new \mock\mageekguy\atoum\locale())
			->and($adapter = new test\adapter())
			->and($generator->getMockController()->getLocale = $locale)
			->and($asserter->getMockController()->call = $asserter)
			->and($asserter->getMockController()->never = $asserter)
			->and($directory->getMockController()->getStream = $adapter)
			->and($locale->getMockController()->_ = $message = uniqid())
			->and($asserter->setWith($directory))
			->then
				->object($asserter->hasNotBeenChecked())->isIdenticalTo($asserter)
				->mock($locale)
					->call('_')->withArguments('directory %s has been checked')
				->mock($asserter)
					->call('call')->withArguments('url_stat')->once()
					->call('never')->withArguments($message)->once()
		;
	}

	public function testHasBeenCreated()
	{
		$this
			->if($generator = new \mock\mageekguy\atoum\asserter\generator())
			->and($asserter = new \mock\mageekguy\atoum\asserters\directory($generator))
			->and($directory = new \mock\mageekguy\atoum\mock\filesystem\directory())
			->and($locale = new \mock\mageekguy\atoum\locale())
			->and($adapter = new test\adapter())
			->and($generator->getMockController()->getLocale = $locale)
			->and($asserter->getMockController()->call = $asserter)
			->and($asserter->getMockController()->once = $asserter)
			->and($directory->getMockController()->getStream = $adapter)
			->and($locale->getMockController()->_ = $message = uniqid())
			->and($asserter->setWith($directory))
			->then
				->object($asserter->hasBeenCreated())->isIdenticalTo($asserter)
				->mock($locale)
					->call('_')->withArguments('directory %s has not been created')
				->mock($asserter)
					->call('call')->withArguments('mkdir')->once()
					->call('once')->withArguments($message)->once()
		;
	}

	public function testHasNotBeenCreated()
	{
		$this
			->if($generator = new \mock\mageekguy\atoum\asserter\generator())
			->and($asserter = new \mock\mageekguy\atoum\asserters\directory($generator))
			->and($directory = new \mock\mageekguy\atoum\mock\filesystem\directory())
			->and($locale = new \mock\mageekguy\atoum\locale())
			->and($adapter = new test\adapter())
			->and($generator->getMockController()->getLocale = $locale)
			->and($asserter->getMockController()->call = $asserter)
			->and($asserter->getMockController()->never = $asserter)
			->and($directory->getMockController()->getStream = $adapter)
			->and($locale->getMockController()->_ = $message = uniqid())
			->and($asserter->setWith($directory))
			->then
				->object($asserter->hasNotBeenCreated())->isIdenticalTo($asserter)
				->mock($locale)
					->call('_')->withArguments('directory %s has been created')
				->mock($asserter)
					->call('call')->withArguments('mkdir')->once()
					->call('never')->withArguments($message)->once()
		;
	}
}
