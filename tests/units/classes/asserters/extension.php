<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter
;

require_once __DIR__ . '/../../runner.php';

class extension extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->variable($this->testedInstance->getName())->isNull()

			->if($this->newTestedInstance($generator = new asserter\generator(), $locale = new atoum\locale()))
			->then
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
				->variable($this->testedInstance->getName())->isNull()
		;
	}

	public function test__toString()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->castToString($this->testedInstance)->isEmpty()

			->if($this->testedInstance->setWith($extensionName = uniqid()))
			->then
				->castToString($this->testedInstance)->isEqualTo($extensionName)
		;
	}

	public function testSetWith()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setWith($extensionName = uniqid()))->isTestedInstance
				->string($this->testedInstance->getName())->isEqualTo($extensionName)
		;
	}

	public function testReset()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->reset())->isTestedInstance
				->variable($this->testedInstance->getName())->isNull()

			->if($this->testedInstance->setWith(uniqid()))
			->then
				->object($this->testedInstance->reset())->isTestedInstance
				->variable($this->testedInstance->getName())->isNull()
		;
	}

	public function testIsLoaded()
	{
		$this
			->given($asserter = $this->newTestedInstance)

			->if($this->function->extension_loaded->doesNothing)
			->then
				->exception(function() use ($asserter) {
						$asserter->isLoaded();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Name of PHP extension is undefined')
				->exception(function() use ($asserter) {
						$asserter->isLoaded;
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Name of PHP extension is undefined')

			->given(
				$extensionName = uniqid(),
				$extension = new \mock\mageekguy\atoum\php\extension($extensionName),
				$this->calling($extension)->isLoaded = false
			)
			->if(
				$this->testedInstance->setPhpExtensionFactory(function() use ($extension) { return $extension; }),
				$this->testedInstance->setWith($extensionName)
			)
			->then
				->exception(function() use ($asserter) {
						$asserter->isLoaded();
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('PHP extension \'' . $extensionName . '\' is not loaded')
				->exception(function() use ($asserter) {
						$asserter->isLoaded;
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('PHP extension \'' . $extensionName . '\' is not loaded')

			->if($this->calling($extension)->isLoaded = true)
			->then
				->object($this->testedInstance->isLoaded())->isTestedInstance
				->object($this->testedInstance->isLoaded)->isTestedInstance
		;
	}
}
