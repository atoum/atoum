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
			->if($asserter = new sut())
			->and($test = $this)
			->then
				->exception(function() use ($asserter, $test) { $asserter->setWithTest($test); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($asserter->setWith($function = uniqid()))
			->then
				->object($asserter->setWithTest($this))->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isCloneOf(php\mocker::getAdapter())
				->string($asserter->getCall()->getFunction())->isEqualTo($this->getTestedClassNamespace() . '\\' . $function)
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut())
			->then
				->object($asserter->setWith($function = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isCloneOf(php\mocker::getAdapter())
				->string($asserter->getCall()->getFunction())->isEqualTo($function)
		;
	}

	public function testIsCalled()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isCalled(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($asserter->setWith(uniqid()))
			->then
				->object($asserter->isCalled())->isIdenticalTo($asserter)
				->variable($asserter->getCall()->getArguments())->isNull()
			->if($asserter->isCalledWithArguments(array()))
			->then
				->object($asserter->isCalled())->isIdenticalTo($asserter)
				->variable($asserter->getCall()->getArguments())->isNull()
		;
	}

	public function testIsCalledWithArguments()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isCalledWithArguments(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($this->function->md5 = uniqid())
			->and($asserter->setWith('md5'))
			->and($asserter->setWithTest($this))
			->then
				->object($asserter->isCalledWithArguments($arg1 = '1', $arg2 = '2'))->isIdenticalTo($asserter)
				->array($asserter->getCall()->getArguments())->isEqualTo(array($arg1, $arg2))
			->if(eval('\\' . $this->getTestedClassNamespace() . '\md5(\'' . $arg1 . '\', \'' . $arg2 . '\');'))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if(eval('\\' . $this->getTestedClassNamespace() . '\md5(1, 2);'))
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
		;
	}

	public function testIsCalledWithIdenticalArguments()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isCalledWithIdenticalArguments(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($this->function->md5 = uniqid())
			->and($asserter->setWith('md5'))
			->and($asserter->setWithTest($this))
			->then
				->object($asserter->isCalledWithIdenticalArguments($arg1 = '1', $arg2 = '2'))->isIdenticalTo($asserter)
				->array($asserter->getCall()->getArguments())->isEqualTo(array($arg1, $arg2))
			->if(eval('\\' . $this->getTestedClassNamespace() . '\md5(1, 2);'))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
		;
	}

	public function testIsCalledWithAnyArguments()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isCalledWithAnyArguments(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($asserter->setWith(uniqid()))
			->and($asserter->isCalledWithArguments(array()))
			->then
				->object($asserter->isCalledWithAnyArguments())->isIdenticalTo($asserter)
				->variable($asserter->getCall()->getArguments())->isNull()
		;
	}

	public function testIsCalledWithoutAnyArguments()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isCalledWithoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($asserter->setWith(uniqid()))
			->and($asserter->isCalledWithArguments(array()))
			->then
				->object($asserter->isCalledWithoutAnyArgument())->isIdenticalTo($asserter)
				->array($asserter->getCall()->getArguments())->isEmpty()
		;
	}
}
