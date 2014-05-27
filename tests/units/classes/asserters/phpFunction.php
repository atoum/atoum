<?php

namespace mageekguy\atoum\tests\units\asserters;

require __DIR__ . '/../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php
;

class phpFunction extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\adapter\call');
	}

	public function testSetWithTest()
	{
		$this
			->given($asserter = $this->newTestedInstance)

			->if($test = new \mock\atoum\test())
			->then
				->object($this->testedInstance->setWithTest($test))->isTestedInstance
				->object($this->testedInstance->getTest())->isIdenticalTo($test)
				->variable($this->testedInstance->getAdapter())->isNull
				->variable($this->testedInstance->getFunction())->isNull

			->if(
				$this->calling($test)->getTestedClassNamespace = uniqid(),
				$this->testedInstance->setWith($function = uniqid()),
				$this->calling($test)->getTestedClassNamespace = uniqid()
			)
			->then
				->object($this->testedInstance->setWithTest($test))->isTestedInstance
				->object($this->testedInstance->getTest())->isIdenticalTo($test)
				->string($this->testedInstance->getFunction())->isEqualTo($test->getTestedClassNamespace() . '\\' . $function)
				->object($this->testedInstance->getAdapter())->isCloneOf(php\mocker::getAdapter())
		;
	}

	public function testSetWith()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->setWith($function = uniqid()))->isTestedInstance
				->string($this->testedInstance->getFunction())->isEqualTo($function)
				->object($this->testedInstance->getAdapter())->isCloneOf(php\mocker::getAdapter())
		;
	}

	public function testWasCalled()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->wasCalled(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')

				->exception(function() use ($asserter) { $asserter->wasCalled; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')

			->if($this->testedInstance->setWith(uniqid()))
			->then
				->object($this->testedInstance->wasCalled())->isTestedInstance
				->variable($this->testedInstance->getArguments())->isNull()

				->object($this->testedInstance->wasCalled)->isTestedInstance
				->variable($this->testedInstance->getArguments())->isNull()

			->if($this->testedInstance->wasCalledWithArguments(range(1, 5)))
			->then
				->object($this->testedInstance->wasCalled())->isTestedInstance
				->object($this->testedInstance->wasCalled)->isTestedInstance
				->variable($this->testedInstance->getCall()->getArguments())->isNull()
		;
	}

	public function testWasCalledWithArguments()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->wasCalledWithArguments(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')

			->if(
				$this->function->md5->doesNothing(),
				$this->testedInstance->setWith('md5')
			)
			->then
				->object($this->testedInstance->wasCalledWithArguments($arg1 = '1', $arg2 = '2'))->isTestedInstance
				->array($this->testedInstance->getCall()->getArguments())->isEqualTo(array($arg1, $arg2))

			->if(eval('\\' . $this->getTestedClassNamespace() . '\md5(\'' . $arg1 . '\', \'' . $arg2 . '\');'))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')

			->if(
				$this->testedInstance->setWithTest($this),
				$this->testedInstance->setWith('md5')
			)
			->then
				->object($this->testedInstance->once)->isTestedInstance

			->if(
				eval('\\' . $this->getTestedClassNamespace() . '\md5(1, 2);'),
				$this->testedInstance->setWith('md5')
			)
			->then
				->object($this->testedInstance->twice())->isTestedInstance
		;
	}

	public function testWasCalledWithIdenticalArguments()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->wasCalledWithIdenticalArguments(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')

			->if(
				$this->function->foo->doesNothing(),
				$this->testedInstance->setWith('foo')
			)
			->then
				->object($this->testedInstance->wasCalledWithIdenticalArguments($arg1 = 1, $arg2 = 2))->isTestedInstance
				->array($this->testedInstance->getArguments())->isEqualTo(array($arg1, $arg2))

			->if(eval('\\' . $this->getTestedClassNamespace() . '\foo(\'1\', \'2\');'))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')

			->given(
				$test = new \mock\atoum\test(),
				$this->calling($test)->getTestedClassNamespace = $this->getTestedClassNamespace()
			)
			->if(
				$this->testedInstance->setWithTest($test),
				$this->testedInstance->setWith('foo')
			)
			->then
				->object($this->testedInstance->wasCalledWithIdenticalArguments($arg1 = 1, $arg2 = 2))->isTestedInstance
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')

			->if(
				eval('\\' . $this->getTestedClassNamespace() . '\foo(1, 2);'),
				$this->testedInstance->setWithTest($test),
				$this->testedInstance->setWith('foo')
			)
			->then
				->object($this->testedInstance->wasCalledWithIdenticalArguments($arg1 = 1, $arg2 = 2))->isTestedInstance
				->object($this->testedInstance->once)->isTestedInstance
		;
	}

	public function testWasCalledWithAnyArguments()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->wasCalledWithAnyArguments(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')

			->if(
				$this->testedInstance
					->setWith(uniqid())
					->wasCalledWithArguments(array())
			)
			->then
				->object($this->testedInstance->wasCalledWithAnyArguments())->isTestedInstance
				->variable($this->testedInstance->getArguments())->isNull()
		;
	}

	public function testWasCalledWithoutAnyArguments()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->wasCalledWithoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')

			->if(
				$this->testedInstance->setWith(uniqid()),
				$this->testedInstance->wasCalledWithArguments(range(1, 5))
			)
			->then
				->object($this->testedInstance->wasCalledWithoutAnyArgument())->isTestedInstance
				->array($this->testedInstance->getArguments())->isEmpty()
		;
	}
}
