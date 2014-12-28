<?php

namespace mageekguy\atoum\tests\units\test\adapter;

require __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\test\adapter\calls as testedClass,
	mock\mageekguy\atoum\test\adapter\calls as mockedTestedClass
;

class calls extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->implements('countable')
			->implements('arrayAccess')
			->implements('iteratorAggregate')
		;
	}

	public function test__construct()
	{
		$this
			->if($calls = new testedClass())
			->then
				->sizeof($calls)->isZero()
		;
	}

	public function test__invoke()
	{
		$this
			->if($calls = new testedClass())
			->then
				->array($calls())->isEmpty()
			->if($calls[] = $call = new adapter\call(uniqid()))
			->then
				->array($calls())->isEqualTo(array($call->getFunction() => array(1 => $call)))
					->object[$call->getFunction()][1]->isIdenticalTo($call)
			->if($calls[] = $otherCall = new adapter\call($call->getFunction()))
			->then
				->array($calls())->isEqualTo(array($call->getFunction() => array(1 => $call, 2 => $otherCall)))
					->object[$call->getFunction()][1]->isIdenticalTo($call)
					->object[$call->getFunction()][2]->isIdenticalTo($otherCall)
					->object[$otherCall->getFunction()][2]->isIdenticalTo($otherCall)
			->if($calls[] = $anotherCall = new adapter\call(uniqid()))
			->then
				->array($calls())->isEqualTo(array(
							$call->getFunction() => array(1 => $call, 2 => $otherCall),
							$anotherCall->getFunction() => array(3 => $anotherCall)
						)
				)
					->object[$call->getFunction()][1]->isIdenticalTo($call)
					->object[$call->getFunction()][2]->isIdenticalTo($otherCall)
					->object[$otherCall->getFunction()][2]->isIdenticalTo($otherCall)
					->object[$anotherCall->getFunction()][3]->isIdenticalTo($anotherCall)
		;
	}

	public function test__toString()
	{
		$this
			->if($calls = new testedClass())
			->then
				->castToString($calls)->isEqualTo($calls->getDecorator()->decorate($calls))
		;
	}

	public function testCount()
	{
		$this
			->if($calls = new testedClass())
			->then
				->sizeof($calls)->isZero()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->sizeof($calls)->isEqualTo(1)
			->if($otherCalls = new testedClass())
			->and($otherCalls[] = $call2 = new adapter\call(uniqid()))
			->then
				->sizeof($calls)->isEqualTo(1)
				->sizeof($otherCalls)->isEqualTo(1)
			->if($calls[] = $call2 = new adapter\call(uniqid()))
			->then
				->sizeof($calls)->isEqualTo(2)
				->sizeof($otherCalls)->isEqualTo(1)
			->if($calls[] = $call3 = new adapter\call($call1->getFunction()))
			->then
				->sizeof($calls)->isEqualTo(3)
				->sizeof($otherCalls)->isEqualTo(1)
		;
	}

	public function testSetDecorator()
	{
		$this
			->if($calls = new testedClass())
			->then
				->object($calls->setDecorator($decorator = new adapter\calls\decorator()))->isIdenticalTo($calls)
				->object($calls->getDecorator())->isIdenticalTo($decorator)
				->object($calls->setDecorator())->isIdenticalTo($calls)
				->object($calls->getDecorator())
					->isNotIdenticalTo($decorator)
					->isEqualTo(new adapter\calls\decorator())
		;
	}

	public function testReset()
	{
		$this
			->if($calls = new testedClass())
			->then
				->object($calls->reset())->isIdenticalTo($calls)
				->sizeof($calls)->isZero()
			->if($calls[] = new adapter\call(uniqid()))
			->then
				->object($calls->reset())->isIdenticalTo($calls)
				->sizeof($calls)->isZero()
			->if($calls[] = $call = new adapter\call(uniqid()))
			->then
				->array($calls[$call->getFunction()]->toArray())->isEqualTo(array(2 => $call))
		;
	}

	public function testAddCall()
	{
		$this
			->if($calls = new testedClass())
			->then
				->object($calls->addCall($call = new adapter\call(uniqid())))->isIdenticalTo($calls)
				->array($calls[$call->getFunction()]->toArray())
					->isEqualTo(array(1 => $call))
						->object[1]->isIdenticalTo($call)
		;
	}

	public function testRemoveCall()
	{
		$this
			->if($calls = new testedClass())
			->then
				->object($calls->removeCall(new adapter\call(uniqid()), rand(0, PHP_INT_MAX)))->isIdenticalTo($calls)
				->sizeof($calls)->isZero()
			->if($calls->addCall($call = new adapter\call(uniqid())))
			->then
				->object($calls->removeCall(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))->isIdenticalTo($calls)
				->sizeof($calls)->isEqualTo(1)
				->object($calls->removeCall($call, rand(2, PHP_INT_MAX)))->isIdenticalTo($calls)
				->sizeof($calls)->isEqualTo(1)
				->object($calls->removeCall($call, 1))->isIdenticalTo($calls)
				->sizeof($calls)->isZero()
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($calls = new testedClass())
			->then
				->exception(function() use ($calls) { $calls[] = new adapter\call(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Function is undefined')
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->array($calls[$call1]->toArray())
					->isEqualTo(array(1 => $call1))
						->object[1]->isIdenticalTo($call1)
			->if($calls[] = $call2 = new adapter\call(uniqid(), array()))
			->then
				->array($calls[$call1]->toArray())
					->isEqualTo(array(1 => $call1))
						->object[1]->isIdenticalTo($call1)
				->array($calls[$call2]->toArray())
					->isEqualTo(array(2 => $call2))
						->object[2]->isIdenticalTo($call2)
			->if($calls[] = $call3 = new adapter\call($call1->getFunction(), array()))
			->then
				->array($calls[$call1]->toArray())
					->isEqualTo(array(1 => $call1, 3 => $call3))
						->object[1]->isIdenticalTo($call1)
						->object[3]->isIdenticalTo($call3)
				->array($calls[$call2]->toArray())
					->isEqualTo(array(2 => $call2))
						->object[2]->isIdenticalTo($call2)
			->if($calls[] = $call4 = new adapter\call(uniqid()))
			->then
				->array($calls[$call1]->toArray())
					->isEqualTo(array(1 => $call1, 3 => $call3))
						->object[1]->isIdenticalTo($call1)
						->object[3]->isIdenticalTo($call3)
				->array($calls[$call2]->toArray())
					->isEqualTo(array(2 => $call2))
						->object[2]->isIdenticalTo($call2)
				->array($calls[$call4->getFunction()]->toArray())
					->isEqualTo(array(4 => $call4))
						->object[4]->isIdenticalTo($call4)
			->if($calls[$newFunction = uniqid()] = $call5 = new adapter\call(uniqid()))
			->then
				->array($calls[$newFunction]->toArray())->isEqualTo(array(5 => $call5))
					->object[5]->isIdenticalTo($call5)
				->string($call5->getFunction())->isEqualTo($newFunction)
		;
	}

	public function testOffsetGet()
	{
		$this
			->if($calls = new testedClass())
			->then
				->array($calls[uniqid()]->toArray())->isEmpty()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->array($calls[$call1->getFunction()]->toArray())
					->isEqualTo(array(1 => $call1))
						->object[1]->isIdenticalTo($call1)
				->array($calls[$call1]->toArray())
					->isEqualTo(array(1 => $call1))
						->object[1]->isIdenticalTo($call1)
			->if($calls[] = $call2 = new adapter\call($call1->getFunction(), array()))
			->then
				->array($calls[uniqid()]->toArray())->isEmpty()
				->array($calls[$call1->getFunction()]->toArray())
					->isEqualTo(array(1 => $call1, 2 => $call2))
						->object[1]->isIdenticalTo($call1)
						->object[2]->isIdenticalTo($call2)
				->array($calls[$call1]->toArray())
					->isEqualTo(array(1 => $call1, 2 => $call2))
						->object[1]->isIdenticalTo($call1)
						->object[2]->isIdenticalTo($call2)
				->array($calls[$call2->getFunction()]->toArray())
					->isEqualTo(array(1 => $call1, 2 => $call2))
						->object[1]->isIdenticalTo($call1)
						->object[2]->isIdenticalTo($call2)
				->array($calls[$call2]->toArray())
					->isEqualTo(array(2 => $call2))
						->object[2]->isIdenticalTo($call2)
		;
	}

	public function testOffsetExists()
	{
		$this
			->if($calls = new testedClass())
			->then
				->boolean(isset($calls[uniqid()]))->isFalse()
			->if($calls[] = $call = new adapter\call(uniqid()))
			->then
				->boolean(isset($calls[uniqid()]))->isFalse()
				->boolean(isset($calls[$call->getFunction()]))->isTrue()
				->boolean(isset($calls[$call]))->isTrue()
		;
	}

	public function testOffsetUnset()
	{
		$this
			->if($calls = new testedClass())
			->when(function() use ($calls) { unset($calls[uniqid()]); })
			->then
				->sizeof($calls)->isZero
			->if($calls[] = $call = new adapter\call(uniqid()))
			->when(function() use ($calls) { unset($calls[uniqid()]); })
			->then
				->boolean(isset($calls[$call->getFunction()]))->isTrue()
				->sizeof($calls)->isEqualTo(1)
			->when(function() use ($calls, $call) { unset($calls[$call->getFunction()]); })
			->then
				->boolean(isset($calls[$call->getFunction()]))->isFalse()
				->sizeof($calls)->isZero
		;
	}

	public function testGetIterator()
	{
		$this
			->if($calls = new testedClass())
			->then
				->object($calls->getIterator())->isEqualTo(new \arrayIterator($calls()))
		;
	}

	public function testToArray()
	{
		$this
			->if($calls = new testedClass())
			->then
				->array($calls->toArray())->isEmpty()
				->array($calls->toArray(new adapter\call(uniqid())))->isEmpty()
			->if($calls->addCall($call1 = new adapter\call(uniqid())))
			->then
				->array($calls->toArray())->isEqualTo(array(1 => $call1))
				->array($calls->toArray(new adapter\call(uniqid())))->isEmpty()
				->array($calls->toArray($call1))->isEqualTo(array(1 => $call1))
			->if($calls->addCall($call2 = clone $call1))
			->then
				->array($calls->toArray())->isEqualTo(array(1 => $call1, 2 => $call2))
				->array($calls->toArray(new adapter\call(uniqid())))->isEmpty()
				->array($calls->toArray($call1))->isEqualTo(array(1 => $call1, 2 => $call2))
				->array($calls->toArray($call2))->isEqualTo(array(1 => $call1, 2 => $call2))
			->if($calls->addCall($call3 = new adapter\call(uniqid())))
			->then
				->array($calls->toArray())->isEqualTo(array(1 => $call1, 2 => $call2, 3 => $call3))
				->array($calls->toArray(new adapter\call(uniqid())))->isEmpty()
				->array($calls->toArray($call1))->isEqualTo(array(1 => $call1, 2 => $call2))
				->array($calls->toArray($call2))->isEqualTo(array(1 => $call1, 2 => $call2))
				->array($calls->toArray($call3))->isEqualTo(array(3 => $call3))
		;
	}

	public function testGetEqualTo()
	{
		$this
			->if($calls = new testedClass())
			->then
				->object($calls->getEqualTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->object($calls->getEqualTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getEqualTo($call1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getEqualTo($call1)->toArray())
						->isEqualTo(array(1 => $call1))
			->if($calls[] = $call2 = new adapter\call($call1->getFunction(), array()))
			->then
				->object($calls->getEqualTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getEqualTo($call1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(2)
					->array($calls->getEqualTo($call1)->toArray())
						->isEqualTo(array(1 => $call1, 2 => $call2))
				->object($calls->getEqualTo($call2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getEqualTo($call2)->toArray())
						->isEqualTo(array(2 => $call2))
			->if($calls[] = $call3 = new adapter\call($call1->getFunction(), array($object = new \mock\object())))
			->then
				->object($calls->getEqualTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getEqualTo($call1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(3)
					->array($calls->getEqualTo($call1)->toArray())
						->isEqualTo(array(1 => $call1, 2 => $call2, 3 => $call3))
				->object($calls->getEqualTo($call2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getEqualTo($call2)->toArray())
						->isEqualTo(array(2 => $call2))
				->object($calls->getEqualTo($call3))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getEqualTo($call3)->toArray())
						->isEqualTo(array(3 => $call3))
			->if($calls[] = $call4 = new adapter\call($call1->getFunction(), array($object = new \mock\object(), $arg = uniqid())))
			->then
				->object($calls->getEqualTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getEqualTo($call1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(4)
					->array($calls->getEqualTo($call1)->toArray())
						->isEqualTo(array(1 => $call1, 2 => $call2, 3 => $call3, 4 => $call4))
				->object($calls->getEqualTo($call2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getEqualTo($call2)->toArray())
						->isEqualTo(array(2 => $call2))
				->object($calls->getEqualTo($call3))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(2)
					->array($calls->getEqualTo($call3)->toArray())
						->isEqualTo(array(3 => $call3, 4 => $call4))
				->object($calls->getEqualTo(new adapter\call($call1->getFunction(), array(clone $object))))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(2)
					->array($calls->getEqualTo($call3)->toArray())
						->isEqualTo(array(3 => $call3, 4 => $call4))
				->object($calls->getEqualTo($call4))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getEqualTo($call4)->toArray())
						->isEqualTo(array(4 => $call4))
				->object($calls->getEqualTo(new adapter\call($call1->getFunction(), array(clone $object, $arg))))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getEqualTo($call4)->toArray())
						->isEqualTo(array(4 => $call4))
			->if($calls = new testedClass())
			->and($calls[] = $call5 = new adapter\call(uniqid(), array(1, 2, 3, 4, 5)))
			->then
				->object($calls->getEqualTo(new adapter\call($call5->getFunction())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getEqualTo(new adapter\call($call5->getFunction()))->toArray())
						->isEqualTo(array(5 => $call5))
		;
	}

	public function testGetIdenticalTo()
	{
		$this
			->if($calls = new testedClass())
			->then
				->object($calls->getIdenticalTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->object($calls->getIdenticalTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getIdenticalTo($call1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call1)->toArray())
						->isEqualTo(array(1 => $call1))
			->if($calls[] = $call2 = new adapter\call($call1->getFunction(), array()))
			->then
				->object($calls->getIdenticalTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getIdenticalTo($call1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(2)
					->array($calls->getIdenticalTo($call1)->toArray())
						->isEqualTo(array(1 => $call1, 2 => $call2))
				->object($calls->getIdenticalTo($call2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call2)->toArray())
						->isEqualTo(array(2 => $call2))
			->if($calls[] = $call3 = new adapter\call($call1->getFunction(), array($object = new \mock\object())))
			->then
				->object($calls->getIdenticalTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getIdenticalTo($call1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(3)
					->array($calls->getIdenticalTo($call1)->toArray())
						->isEqualTo(array(1 => $call1, 2 => $call2, 3 => $call3))
				->object($calls->getIdenticalTo($call2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call2)->toArray())
						->isEqualTo(array(2 => $call2))
				->object($calls->getIdenticalTo($call3))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call3)->toArray())
						->isEqualTo(array(3 => $call3))
				->object($calls->getIdenticalTo(new adapter\call($call1->getFunction(), array(clone $object))))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call4 = new adapter\call($call1->getFunction(), array($object = new \mock\object(), $arg = uniqid())))
			->then
				->object($calls->getIdenticalTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getIdenticalTo($call1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(4)
					->array($calls->getIdenticalTo($call1)->toArray())
						->isEqualTo(array(1 => $call1, 2 => $call2, 3 => $call3, 4 => $call4))
				->object($calls->getIdenticalTo($call2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call2)->toArray())
						->isEqualTo(array(2 => $call2))
				->object($calls->getIdenticalTo($call3))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call3)->toArray())
						->isEqualTo(array(3 => $call3))
				->object($calls->getIdenticalTo(new adapter\call($call1->getFunction(), array(clone $object))))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getIdenticalTo($call4))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call4)->toArray())
						->isEqualTo(array(4 => $call4))
				->object($calls->getIdenticalTo(new adapter\call($call1->getFunction(), array(clone $object, $arg))))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
		;
	}

	public function testGetPreviousEqualTo()
	{
		$this
			->if($calls = new testedClass())
			->then
				->object($calls->getPreviousEqualTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->object($calls->getPreviousEqualTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousEqualTo(new adapter\call($call1), 0))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousEqualTo(new adapter\call($call1), 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousEqualTo(new adapter\call($call1), rand(2, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call2 = new adapter\call(uniqid(), array()))
			->then
				->object($calls->getPreviousEqualTo(new adapter\call(uniqid()), 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousEqualTo($call1, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($previousCalls = $calls->getPreviousEqualTo($call1, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($previousCalls->toArray())
						->isEqualTo(array(1 => $call1))
				->object($calls->getPreviousEqualTo($call2, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousEqualTo($call2, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call3 = new adapter\call(uniqid(), array($object = new \mock\object())))
			->if($calls[] = $call4 = new adapter\call($call3->getFunction(), array(clone $object)))
			->and($calls[] = $call5 = new adapter\call(uniqid(), array()))
			->then
				->object($calls->getPreviousEqualTo(new adapter\call(uniqid()), 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousEqualTo($call1, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($previousCalls = $calls->getPreviousEqualTo($call1, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($previousCalls->toArray())
						->isEqualTo(array(1 => $call1))
				->object($calls->getPreviousEqualTo($call2, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousEqualTo($call2, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($previousCalls = $calls->getPreviousEqualTo($call3, 4))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($previousCalls->toArray())
						->isEqualTo(array(3 => $call3))
				->object($previousCalls = $calls->getPreviousEqualTo($call4, 4))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($previousCalls->toArray())
						->isEqualTo(array(3 => $call3))
				->object($previousCalls = $calls->getPreviousEqualTo($call3, 5))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(2)
					->array($previousCalls->toArray())
						->isEqualTo(array(3 => $call3, 4 => $call4))
				->object($previousCalls = $calls->getPreviousEqualTo($call4, 5))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(2)
					->array($previousCalls->toArray())
						->isEqualTo(array(3 => $call3, 4 => $call4))
		;
	}

	public function testGetPreviousIdenticalTo()
	{
		$this
			->if($calls = new testedClass())
			->then
				->object($calls->getPreviousIdenticalTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->object($calls->getPreviousIdenticalTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousIdenticalTo(new adapter\call($call1), 0))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousIdenticalTo(new adapter\call($call1), 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousIdenticalTo(new adapter\call($call1), rand(2, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call2 = new adapter\call(uniqid(), array()))
			->then
				->object($calls->getPreviousIdenticalTo(new adapter\call(uniqid()), 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousIdenticalTo($call1, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($previousCalls = $calls->getPreviousIdenticalTo($call1, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($previousCalls->toArray())
						->isIdenticalTo(array(1 => $call1))
				->object($calls->getPreviousIdenticalTo($call2, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousIdenticalTo($call2, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call3 = new adapter\call(uniqid(), array($object = new \mock\object())))
			->if($calls[] = $call4 = new adapter\call($call3->getFunction(), array(clone $object)))
			->and($calls[] = $call5 = new adapter\call(uniqid(), array()))
			->then
				->object($calls->getPreviousIdenticalTo(new adapter\call(uniqid()), 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousIdenticalTo($call1, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($previousCalls = $calls->getPreviousIdenticalTo($call1, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($previousCalls->toArray())
						->isIdenticalTo(array(1 => $call1))
				->object($calls->getPreviousIdenticalTo($call2, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getPreviousIdenticalTo($call2, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($previousCalls = $calls->getPreviousIdenticalTo($call3, 4))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($previousCalls->toArray())
						->isIdenticalTo(array(3 => $call3))
				->object($calls->getPreviousIdenticalTo($call4, 4))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($previousCalls = $calls->getPreviousIdenticalTo($call3, 5))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($previousCalls->toArray())
						->isIdenticalTo(array(3 => $call3))
				->object($previousCalls =$calls->getPreviousIdenticalTo($call4, 5))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($previousCalls->toArray())
						->isIdenticalTo(array(4 => $call4))
		;
	}

	public function testGetPrevious()
	{
		$this
			->if($calls = new mockedTestedClass())
			->then
				->object($calls->getPrevious($call = new adapter\call(uniqid()), $position = rand(1, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
				->mock($calls)->call('getPreviousEqualTo')->withArguments($call, $position)->once()
				->object($calls->getPrevious($call = new adapter\call(uniqid()), $position = rand(1, PHP_INT_MAX), true))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
				->mock($calls)->call('getPreviousIdenticalTo')->withArguments($call, $position)->once()
		;
	}

	public function testHasPreviousEqualTo()
	{
		$this
			->if($calls = new testedClass())
			->then
				->boolean($calls->hasPreviousEqualTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))->isFalse()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->boolean($calls->hasPreviousEqualTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))->isFalse()
				->boolean($calls->hasPreviousEqualTo(new adapter\call($call1), 0))->isFalse()
				->boolean($calls->hasPreviousEqualTo(new adapter\call($call1), 1))->isFalse()
				->boolean($calls->hasPreviousEqualTo(new adapter\call($call1), rand(2, PHP_INT_MAX)))->isFalse()
			->if($calls[] = $call2 = new adapter\call(uniqid(), array()))
			->then
				->boolean($calls->hasPreviousEqualTo(new adapter\call(uniqid()), 1))->isFalse()
				->boolean($calls->hasPreviousEqualTo($call1, 1))->isFalse()
				->boolean($previousCalls = $calls->hasPreviousEqualTo($call1, 2))->isTrue()
				->boolean($calls->hasPreviousEqualTo($call2, 1))->isFalse()
				->boolean($calls->hasPreviousEqualTo($call2, 2))->isFalse()
			->if($calls[] = $call3 = new adapter\call(uniqid(), array($object = new \mock\object())))
			->if($calls[] = $call4 = new adapter\call($call3->getFunction(), array(clone $object)))
			->and($calls[] = $call5 = new adapter\call(uniqid(), array()))
			->then
				->boolean($calls->hasPreviousEqualTo(new adapter\call(uniqid()), 1))->isFalse()
				->boolean($calls->hasPreviousEqualTo($call1, 1))->isFalse()
				->boolean($previousCalls = $calls->hasPreviousEqualTo($call1, 2))->isTrue()
				->boolean($calls->hasPreviousEqualTo($call2, 1))->isFalse()
				->boolean($calls->hasPreviousEqualTo($call2, 2))->isFalse()
				->boolean($previousCalls = $calls->hasPreviousEqualTo($call3, 4))->isTrue()
				->boolean($previousCalls = $calls->hasPreviousEqualTo($call4, 4))->isTrue()
				->boolean($previousCalls = $calls->hasPreviousEqualTo($call3, 5))->isTrue()
				->boolean($previousCalls = $calls->hasPreviousEqualTo($call4, 5))->isTrue()
		;
	}

	public function testHasPreviousIdenticalTo()
	{
		$this
			->if($calls = new testedClass())
			->then
				->boolean($calls->hasPreviousIdenticalTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))->isFalse()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->boolean($calls->hasPreviousIdenticalTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))->isFalse()
				->boolean($calls->hasPreviousIdenticalTo(new adapter\call($call1), 0))->isFalse()
				->boolean($calls->hasPreviousIdenticalTo(new adapter\call($call1), 1))->isFalse()
				->boolean($calls->hasPreviousIdenticalTo(new adapter\call($call1), rand(2, PHP_INT_MAX)))->isFalse()
			->if($calls[] = $call2 = new adapter\call(uniqid(), array()))
			->then
				->boolean($calls->hasPreviousIdenticalTo(new adapter\call(uniqid()), 1))->isFalse()
				->boolean($calls->hasPreviousIdenticalTo($call1, 1))->isFalse()
				->boolean($previousCalls = $calls->hasPreviousIdenticalTo($call1, 2))->isTrue()
				->boolean($calls->hasPreviousIdenticalTo($call2, 1))->isFalse()
				->boolean($calls->hasPreviousIdenticalTo($call2, 2))->isFalse()
			->if($calls[] = $call3 = new adapter\call(uniqid(), array($object = new \mock\object())))
			->if($calls[] = $call4 = new adapter\call($call3->getFunction(), array(clone $object)))
			->and($calls[] = $call5 = new adapter\call(uniqid(), array()))
			->then
				->boolean($calls->hasPreviousIdenticalTo(new adapter\call(uniqid()), 1))->isFalse()
				->boolean($calls->hasPreviousIdenticalTo($call1, 1))->isFalse()
				->boolean($previousCalls = $calls->hasPreviousIdenticalTo($call1, 2))->isTrue()
				->boolean($calls->hasPreviousIdenticalTo($call2, 1))->isFalse()
				->boolean($calls->hasPreviousIdenticalTo($call2, 2))->isFalse()
				->boolean($previousCalls = $calls->hasPreviousIdenticalTo($call3, 4))->isTrue()
				->boolean($calls->hasPreviousIdenticalTo($call4, 4))->isFalse()
				->boolean($previousCalls = $calls->hasPreviousIdenticalTo($call3, 5))->isTrue()
				->boolean($previousCalls =$calls->hasPreviousIdenticalTo($call4, 5))->isTrue()
		;
	}

	public function testHasPrevious()
	{
		$this
			->if($calls = new mockedTestedClass())
			->then
				->boolean($calls->hasPrevious($call = new adapter\call(uniqid()), $position = rand(1, PHP_INT_MAX)))->isFalse()
				->mock($calls)->call('hasPreviousEqualTo')->withArguments($call, $position)->once()
				->boolean($calls->hasPrevious($call = new adapter\call(uniqid()), $position = rand(1, PHP_INT_MAX), true))->isFalse()
				->mock($calls)->call('hasPreviousIdenticalTo')->withArguments($call, $position)->once()
		;
	}

	public function testGetAfterEqualTo()
	{
		$this
			->if($calls = new testedClass())
			->then
				->object($calls->getAfterEqualTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->object($calls->getAfterEqualTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterEqualTo(new adapter\call($call1), 0))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterEqualTo(new adapter\call($call1), 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterEqualTo(new adapter\call($call1), rand(2, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call2 = new adapter\call(uniqid(), array()))
			->then
				->object($calls->getAfterEqualTo(new adapter\call(uniqid()), 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterEqualTo($call1, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterEqualTo($call1, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($afterCalls = $calls->getAfterEqualTo($call2, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($afterCalls->toArray())
						->isEqualTo(array(2 => $call2))
				->object($calls->getAfterEqualTo($call2, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call3 = new adapter\call(uniqid(), array($object = new \mock\object())))
			->if($calls[] = $call4 = new adapter\call($call3->getFunction(), array(clone $object)))
			->and($calls[] = $call5 = new adapter\call(uniqid(), array()))
			->then
				->object($calls->getAfterEqualTo(new adapter\call(uniqid()), 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterEqualTo($call1, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterEqualTo($call1, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($afterCalls = $calls->getAfterEqualTo($call2, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($afterCalls->toArray())
						->isEqualTo(array(2 => $call2))
				->object($calls->getAfterEqualTo($call2, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($afterCalls = $calls->getAfterEqualTo($call3, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(2)
					->array($afterCalls->toArray())
						->isEqualTo(array(3 => $call3, 4 => $call4))
				->object($afterCalls = $calls->getAfterEqualTo($call3, 3))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($afterCalls->toArray())
						->isEqualTo(array(4 => $call4))
				->object($afterCalls = $calls->getAfterEqualTo($call4, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(2)
					->array($afterCalls->toArray())
						->isEqualTo(array(3 => $call3, 4 => $call4))
		;
	}

	public function testGetAfterIdenticalTo()
	{
		$this
			->if($calls = new testedClass())
			->then
				->object($calls->getAfterIdenticalTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->object($calls->getAfterIdenticalTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterIdenticalTo(new adapter\call($call1), 0))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterIdenticalTo(new adapter\call($call1), 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterIdenticalTo(new adapter\call($call1), rand(2, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call2 = new adapter\call(uniqid(), array()))
			->then
				->object($calls->getAfterIdenticalTo(new adapter\call(uniqid()), 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterIdenticalTo($call1, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterIdenticalTo($call1, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($afterCalls = $calls->getAfterIdenticalTo($call2, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($afterCalls->toArray())
						->isIdenticalTo(array(2 => $call2))
				->object($calls->getAfterIdenticalTo($call2, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call3 = new adapter\call(uniqid(), array($object = new \mock\object())))
			->if($calls[] = $call4 = new adapter\call($call3->getFunction(), array(clone $object)))
			->and($calls[] = $call5 = new adapter\call(uniqid(), array()))
			->then
				->object($calls->getAfterIdenticalTo(new adapter\call(uniqid()), 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterIdenticalTo($call1, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getAfterIdenticalTo($call1, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($afterCalls = $calls->getAfterIdenticalTo($call2, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($afterCalls->toArray())
						->isIdenticalTo(array(2 => $call2))
				->object($calls->getAfterIdenticalTo($call2, 2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($afterCalls = $calls->getAfterIdenticalTo($call3, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($afterCalls->toArray())
						->isIdenticalTo(array(3 => $call3))
				->object($afterCalls = $calls->getAfterIdenticalTo($call3, 3))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($afterCalls = $calls->getAfterIdenticalTo($call4, 1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($afterCalls->toArray())
						->isIdenticalTo(array(4 => $call4))
		;
	}

	public function testGetAfter()
	{
		$this
			->if($calls = new mockedTestedClass())
			->then
				->object($calls->getAfter($call = new adapter\call(uniqid()), $position = rand(1, PHP_INT_MAX)))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
				->mock($calls)->call('getAfterEqualTo')->withArguments($call, $position)->once()
				->object($calls->getAfter($call = new adapter\call(uniqid()), $position = rand(1, PHP_INT_MAX), true))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
				->mock($calls)->call('getAfterIdenticalTo')->withArguments($call, $position)->once()
		;
	}

	public function testHasAfterEqualTo()
	{
		$this
			->if($calls = new testedClass())
			->then
				->boolean($calls->hasAfterEqualTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))->isFalse()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->boolean($calls->hasAfterEqualTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))->isFalse()
				->boolean($calls->hasAfterEqualTo(new adapter\call($call1), 0))->isFalse()
				->boolean($calls->hasAfterEqualTo(new adapter\call($call1), 1))->isFalse()
				->boolean($calls->hasAfterEqualTo(new adapter\call($call1), rand(2, PHP_INT_MAX)))->isFalse()
			->if($calls[] = $call2 = new adapter\call(uniqid(), array()))
			->then
				->boolean($calls->hasAfterEqualTo(new adapter\call(uniqid()), 1))->isFalse()
				->boolean($calls->hasAfterEqualTo($call1, 1))->isFalse()
				->boolean($calls->hasAfterEqualTo($call1, 2))->isFalse()
				->boolean($afterCalls = $calls->hasAfterEqualTo($call2, 1))->isTrue()
				->boolean($calls->hasAfterEqualTo($call2, 2))->isFalse()
			->if($calls[] = $call3 = new adapter\call(uniqid(), array($object = new \mock\object())))
			->if($calls[] = $call4 = new adapter\call($call3->getFunction(), array(clone $object)))
			->and($calls[] = $call5 = new adapter\call(uniqid(), array()))
			->then
				->boolean($calls->hasAfterEqualTo(new adapter\call(uniqid()), 1))->isFalse()
				->boolean($calls->hasAfterEqualTo($call1, 1))->isFalse()
				->boolean($calls->hasAfterEqualTo($call1, 2))->isFalse()
				->boolean($afterCalls = $calls->hasAfterEqualTo($call2, 1))->isTrue()
				->boolean($calls->hasAfterEqualTo($call2, 2))->isFalse()
				->boolean($afterCalls = $calls->hasAfterEqualTo($call3, 1))->isTrue()
				->boolean($afterCalls = $calls->hasAfterEqualTo($call3, 3))->isTrue()
				->boolean($afterCalls = $calls->hasAfterEqualTo($call4, 1))->isTrue()
		;
	}

	public function testHasAfterIdenticalTo()
	{
		$this
			->if($calls = new testedClass())
			->then
				->boolean($calls->hasAfterIdenticalTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))->isFalse()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->boolean($calls->hasAfterIdenticalTo(new adapter\call(uniqid()), rand(1, PHP_INT_MAX)))->isFalse()
				->boolean($calls->hasAfterIdenticalTo(new adapter\call($call1), 0))->isFalse()
				->boolean($calls->hasAfterIdenticalTo(new adapter\call($call1), 1))->isFalse()
				->boolean($calls->hasAfterIdenticalTo(new adapter\call($call1), rand(2, PHP_INT_MAX)))->isFalse()
			->if($calls[] = $call2 = new adapter\call(uniqid(), array()))
			->then
				->boolean($calls->hasAfterIdenticalTo(new adapter\call(uniqid()), 1))->isFalse()
				->boolean($calls->hasAfterIdenticalTo($call1, 1))->isFalse()
				->boolean($calls->hasAfterIdenticalTo($call1, 2))->isFalse()
				->boolean($afterCalls = $calls->hasAfterIdenticalTo($call2, 1))->isTrue()
				->boolean($calls->hasAfterIdenticalTo($call2, 2))->isFalse()
			->if($calls[] = $call3 = new adapter\call(uniqid(), array($object = new \mock\object())))
			->if($calls[] = $call4 = new adapter\call($call3->getFunction(), array(clone $object)))
			->and($calls[] = $call5 = new adapter\call(uniqid(), array()))
			->then
				->boolean($calls->hasAfterIdenticalTo(new adapter\call(uniqid()), 1))->isFalse()
				->boolean($calls->hasAfterIdenticalTo($call1, 1))->isFalse()
				->boolean($calls->hasAfterIdenticalTo($call1, 2))->isFalse()
				->boolean($afterCalls = $calls->hasAfterIdenticalTo($call2, 1))->isTrue()
				->boolean($calls->hasAfterIdenticalTo($call2, 2))->isFalse()
				->boolean($afterCalls = $calls->hasAfterIdenticalTo($call3, 1))->isTrue()
				->boolean($afterCalls = $calls->hasAfterIdenticalTo($call3, 3))->isFalse()
				->boolean($afterCalls = $calls->hasAfterIdenticalTo($call4, 1))->isTrue()
		;
	}

	public function testHasAfter()
	{
		$this
			->if($calls = new mockedTestedClass())
			->then
				->boolean($calls->hasAfter($call = new adapter\call(uniqid()), $position = rand(1, PHP_INT_MAX)))->isFalse()
				->mock($calls)->call('hasAfterEqualTo')->withArguments($call, $position)->once()
				->boolean($calls->hasAfter($call = new adapter\call(uniqid()), $position = rand(1, PHP_INT_MAX), true))->isFalse()
				->mock($calls)->call('hasAfterIdenticalTo')->withArguments($call, $position)->once()
		;
	}

	public function testGet()
	{
		$this
			->if($calls = new testedClass())
			->then
				->object($calls->get(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->object($calls->get(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->get($call1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->get($call1)->toArray())
						->isEqualTo(array(1 => $call1))
			->if($calls[] = $call2 = new adapter\call($call1->getFunction(), array()))
			->then
				->object($calls->get(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->get($call1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(2)
					->array($calls->get($call1)->toArray())
						->isEqualTo(array(1 => $call1, 2 => $call2))
				->object($calls->get($call2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->get($call2)->toArray())
						->isEqualTo(array(2 => $call2))
			->if($calls[] = $call3 = new adapter\call($call1->getFunction(), array($object = new \mock\object())))
			->then
				->object($calls->get(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->get($call1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(3)
					->array($calls->get($call1)->toArray())
						->isEqualTo(array(1 => $call1, 2 => $call2, 3 => $call3))
				->object($calls->get($call2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->get($call2)->toArray())
						->isEqualTo(array(2 => $call2))
				->object($calls->get($call3))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->get($call3)->toArray())
						->isEqualTo(array(3 => $call3))
			->if($calls[] = $call4 = new adapter\call($call1->getFunction(), array($object = new \mock\object(), $arg = uniqid())))
			->then
				->object($calls->get(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->get($call1))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(4)
					->array($calls->get($call1)->toArray())
						->isEqualTo(array(1 => $call1, 2 => $call2, 3 => $call3, 4 => $call4))
				->object($calls->get($call2))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->get($call2)->toArray())
						->isEqualTo(array(2 => $call2))
				->object($calls->get($call3))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(2)
					->array($calls->get($call3)->toArray())
						->isEqualTo(array(3 => $call3, 4 => $call4))
				->object($calls->get(new adapter\call($call1->getFunction(), array(clone $object))))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(2)
					->array($calls->get($call3)->toArray())
						->isEqualTo(array(3 => $call3, 4 => $call4))
				->object($calls->get($call4))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->get($call4)->toArray())
						->isEqualTo(array(4 => $call4))
				->object($calls->get(new adapter\call($call1->getFunction(), array(clone $object, $arg))))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->get($call4)->toArray())
						->isEqualTo(array(4 => $call4))

			->if($calls = new testedClass())
			->then
				->object($calls->getIdenticalTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call5 = new adapter\call(uniqid()))
			->then
				->object($calls->getIdenticalTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getIdenticalTo($call5))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call5)->toArray())
						->isEqualTo(array(5 => $call5))
			->if($calls[] = $call6 = new adapter\call($call5->getFunction(), array()))
			->then
				->object($calls->getIdenticalTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getIdenticalTo($call5))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(2)
					->array($calls->getIdenticalTo($call5)->toArray())
						->isEqualTo(array(5 => $call5, 6 => $call6))
				->object($calls->getIdenticalTo($call6))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call6)->toArray())
						->isEqualTo(array(6 => $call6))
			->if($calls[] = $call7 = new adapter\call($call5->getFunction(), array($object = new \mock\object())))
			->then
				->object($calls->getIdenticalTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getIdenticalTo($call5))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(3)
					->array($calls->getIdenticalTo($call5)->toArray())
						->isEqualTo(array(5 => $call5, 6 => $call6, 7 => $call7))
				->object($calls->getIdenticalTo($call6))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call6)->toArray())
						->isEqualTo(array(6 => $call6))
				->object($calls->getIdenticalTo($call7))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call7)->toArray())
						->isEqualTo(array(7 => $call7))
				->object($calls->getIdenticalTo(new adapter\call($call5->getFunction(), array(clone $object))))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($calls[] = $call8 = new adapter\call($call5->getFunction(), array($object = new \mock\object(), $arg = uniqid())))
			->then
				->object($calls->getIdenticalTo(new adapter\call(uniqid())))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getIdenticalTo($call5))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(4)
					->array($calls->getIdenticalTo($call5)->toArray())
						->isEqualTo(array(5 => $call5, 6 => $call6, 7 => $call7, 8 => $call8))
				->object($calls->getIdenticalTo($call6))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call6)->toArray())
						->isEqualTo(array(6 => $call6))
				->object($calls->getIdenticalTo($call7))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call7)->toArray())
						->isEqualTo(array(7 => $call7))
				->object($calls->getIdenticalTo(new adapter\call($call5->getFunction(), array(clone $object))))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
				->object($calls->getIdenticalTo($call8))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(1)
					->array($calls->getIdenticalTo($call8)->toArray())
						->isEqualTo(array(8 => $call8))
				->object($calls->getIdenticalTo(new adapter\call($call1->getFunction(), array(clone $object, $arg))))
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
		;
	}

	public function testGetTimeline()
	{
		$this
			->if($calls = new testedClass())
			->then
				->array($calls->getTimeline())->isEmpty()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->array($calls->getTimeline())->isEqualTo(array(1 => $call1))
			->if($calls[] = $call2 = new adapter\call(uniqid()))
			->then
				->array($calls->getTimeline())->isEqualTo(array(
						1 => $call1,
						2 => $call2
					)
				)
			->if($otherCalls = new testedClass())
			->and($otherCalls[] = $call3 = new adapter\call(uniqid()))
			->then
				->array($calls->getTimeline())->isEqualTo(array(
						1 => $call1,
						2 => $call2
					)
				)
			->if($calls[] = $call4 = new adapter\call(uniqid()))
			->then
				->array($calls->getTimeline())->isEqualTo(array(
						1 => $call1,
						2 => $call2,
						4 => $call4
					)
				)
		;
	}
}
