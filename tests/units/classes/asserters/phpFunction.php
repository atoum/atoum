<?php

namespace mageekguy\atoum\tests\units\asserters;

require __DIR__ . '/../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\asserters\phpFunction as sut
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
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->and($test = $this)
			->then
				->exception(function() use ($asserter, $test) { $asserter->setWithTest($test); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($asserter->setWith($function = uniqid()))
			->then
				->object($asserter->disableEvaluationChecking()->setWithTest($this))->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isCloneOf(php\mocker::getAdapter())
				->string($asserter->getCall()->getFunction())->isEqualTo($this->getTestedClassNamespace() . '\\' . $function)
		;
	}

	public function testSetWith()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->object($asserter->disableEvaluationChecking()->setWith($function = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isCloneOf(php\mocker::getAdapter())
				->string($asserter->getCall()->getFunction())->isEqualTo($function)
		;
	}

	public function testWasCalled()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->wasCalled(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($asserter->setWith(uniqid()))
			->then
				->object($asserter->wasCalled())->isIdenticalTo($asserter)
				->variable($asserter->getCall()->getArguments())->isNull()
			->if($asserter->disableEvaluationChecking()->wasCalledWithArguments(array()))
			->then
				->object($asserter->wasCalled())->isIdenticalTo($asserter)
				->variable($asserter->getCall()->getArguments())->isNull()
		;
	}

	public function testWasCalledWithArguments()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->wasCalledWithArguments(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($this->function->md5 = uniqid())
			->and($asserter->setWith('md5'))
			->and($asserter->setWithTest($this))
			->then
				->object($asserter->wasCalledWithArguments($arg1 = '1', $arg2 = '2'))->isIdenticalTo($asserter)
				->array($asserter->getCall()->getArguments())->isEqualTo(array($arg1, $arg2))
			->if(eval('\\' . $this->getTestedClassNamespace() . '\md5(\'' . $arg1 . '\', \'' . $arg2 . '\');'))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
			->if($asserter->setWith('md5'))
			->and($asserter->setWithTest($this))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if(eval('\\' . $this->getTestedClassNamespace() . '\md5(1, 2);'))
			->if($asserter->setWith('md5'))
			->and($asserter->setWithTest($this))
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
		;
	}

	public function testWasCalledWithIdenticalArguments()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->wasCalledWithIdenticalArguments(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($this->function->md5 = uniqid())
			->and($asserter->setWith('md5'))
			->and($asserter->setWithTest($this))
			->then
				->object($asserter->wasCalledWithIdenticalArguments($arg1 = '1', $arg2 = '2'))->isIdenticalTo($asserter)
				->array($asserter->getCall()->getArguments())->isEqualTo(array($arg1, $arg2))
			->if(eval('\\' . $this->getTestedClassNamespace() . '\md5(1, 2);'))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
		;
	}

	public function testWasCalledWithAnyArguments()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->wasCalledWithAnyArguments(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($asserter->setWith(uniqid()))
			->and($asserter->disableEvaluationChecking()->wasCalledWithArguments(array()))
			->then
				->object($asserter->wasCalledWithAnyArguments())->isIdenticalTo($asserter)
				->variable($asserter->getCall()->getArguments())->isNull()
		;
	}

	public function testWasCalledWithoutAnyArguments()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->wasCalledWithoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($asserter->setWith(uniqid()))
			->and($asserter->wasCalledWithArguments(array()))
			->then
				->object($asserter->disableEvaluationChecking()->wasCalledWithoutAnyArgument())->isIdenticalTo($asserter)
				->array($asserter->getCall()->getArguments())->isEmpty()
		;
	}
}
