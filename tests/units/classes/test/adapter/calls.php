<?php

namespace mageekguy\atoum\tests\units\test\adapter;

require __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\test\adapter\calls as testedClass
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
				->array($calls[$call->getFunction()])->isEqualTo(array(2 => $call))
		;
	}

	public function testAddCall()
	{
		$this
			->if($calls = new testedClass())
			->then
				->object($calls->addCall($call = new adapter\call(uniqid())))->isIdenticalTo($calls)
				->array($calls[$call->getFunction()])
					->isEqualTo(array(1 => $call))
						->object[1]->isIdenticalTo($call)
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($calls = new testedClass())
			->then
				->array($calls[uniqid()])->isEmpty()
			->if($calls[] = $call = new adapter\call(uniqid()))
			->then
				->array($calls[$call->getFunction()])
					->isEqualTo(array(1 => $call))
						->object[1]->isIdenticalTo($call)
			->if($calls[] = $otherCall = new adapter\call($call->getFunction()))
			->then
				->array($calls[$call->getFunction()])
					->isEqualTo(array(1 => $call, 2 => $otherCall))
						->object[1]->isIdenticalTo($call)
						->object[2]->isIdenticalTo($otherCall)
			->if($calls[] = $anotherCall = new adapter\call(uniqid()))
			->then
				->array($calls[$call->getFunction()])
					->isEqualTo(array(1 => $call, 2 => $otherCall))
						->object[1]->isIdenticalTo($call)
						->object[2]->isIdenticalTo($otherCall)
				->array($calls[$anotherCall->getFunction()])
					->isEqualTo(array(3 => $anotherCall))
						->object[3]->isIdenticalTo($anotherCall)
			->if($calls[$unusedFunctionName = uniqid()] = $callWithUnusedFuntionName = new adapter\call(uniqid()))
			->then
				->array($calls[$unusedFunctionName])->isEmpty()
				->array($calls[$callWithUnusedFuntionName])
					->isEqualTo(array(4 => $callWithUnusedFuntionName))
						->object[4]->isIdenticalTo($callWithUnusedFuntionName)
		;
	}

	public function testOffsetGet()
	{
		$this
			->if($calls = new testedClass())
			->then
				->array($calls[uniqid()])->isEmpty()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->array($calls[$call1->getFunction()])
					->isEqualTo(array(1 => $call1))
						->object[1]->isIdenticalTo($call1)
				->array($calls[$call1])
					->isEqualTo(array(1 => $call1))
						->object[1]->isIdenticalTo($call1)
			->if($calls[] = $call2 = new adapter\call($call1->getFunction(), array()))
			->then
				->array($calls[uniqid()])->isEmpty()
				->array($calls[$call1->getFunction()])
					->isEqualTo(array(1 => $call1, 2 => $call2))
						->object[1]->isIdenticalTo($call1)
						->object[2]->isIdenticalTo($call2)
				->array($calls[$call1])
					->isEqualTo(array(1 => $call1, 2 => $call2))
						->object[1]->isIdenticalTo($call1)
						->object[2]->isIdenticalTo($call2)
				->array($calls[$call2->getFunction()])
					->isEqualTo(array(1 => $call1, 2 => $call2))
						->object[1]->isIdenticalTo($call1)
						->object[2]->isIdenticalTo($call2)
				->array($calls[$call2])
					->isEqualTo(array(1 => $call1, 2 => $call2))
						->object[1]->isIdenticalTo($call1)
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
				->sizeof($calls)->isZero()
			->if($calls[] = $call = new adapter\call(uniqid()))
			->when(function() use ($calls) { unset($calls[uniqid()]); })
			->then
				->boolean(isset($calls[$call->getFunction()]))->isTrue()
			->when(function() use ($calls, $call) { unset($calls[$call->getFunction()]); })
			->then
				->boolean(isset($calls[$call->getFunction()]))->isFalse()
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

	public function testFindEqual()
	{
		$this
			->if($calls = new testedClass())
			->then
				->array($calls->findEqual(new adapter\call(uniqid())))->isEmpty()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->array($calls->findEqual(new adapter\call(uniqid())))->isEmpty()
				->array($calls->findEqual($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = $call2 = new adapter\call($call1->getFunction(), array()))
			->then
				->array($calls->findEqual(new adapter\call(uniqid())))->isEmpty()
				->array($calls->findEqual($call1))->isIdenticalTo(array(1 => $call1, 2 => $call2))
				->array($calls->findEqual($call2))->isIdenticalTo(array(2 => $call2))
			->if($calls[] = $call3 = new adapter\call($call1->getFunction(), array($object = new \mock\object())))
			->then
				->array($calls->findEqual(new adapter\call(uniqid())))->isEmpty()
				->array($calls->findEqual($call1))->isIdenticalTo(array(1 => $call1, 2 => $call2, 3 => $call3))
				->array($calls->findEqual($call2))->isIdenticalTo(array(2 => $call2))
				->array($calls->findEqual($call3))->isIdenticalTo(array(3 => $call3))
				->array($calls->findEqual(new adapter\call($call1->getFunction(), array(clone $object))))->isIdenticalTo(array(3 => $call3))
			->if($calls[] = $call4 = new adapter\call($call1->getFunction(), array($object = new \mock\object(), $arg = uniqid())))
			->then
				->array($calls->findEqual(new adapter\call(uniqid())))->isEmpty()
				->array($calls->findEqual($call1))->isIdenticalTo(array(1 => $call1, 2 => $call2, 3 => $call3, 4 => $call4))
				->array($calls->findEqual($call2))->isIdenticalTo(array(2 => $call2))
				->array($calls->findEqual($call3))->isIdenticalTo(array(3 => $call3, 4 => $call4))
				->array($calls->findEqual(new adapter\call($call1->getFunction(), array(clone $object))))->isIdenticalTo(array(3 => $call3, 4 => $call4))
				->array($calls->findEqual($call4))->isIdenticalTo(array(4 => $call4))
				->array($calls->findEqual(new adapter\call($call1->getFunction(), array(clone $object, $arg))))->isIdenticalTo(array(4 => $call4))
		;
	}

	public function testFindIdentical()
	{
		$this
			->if($calls = new testedClass())
			->then
				->array($calls->findIdentical(new adapter\call(uniqid())))->isEmpty()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->array($calls->findIdentical(new adapter\call(uniqid())))->isEmpty()
				->array($calls->findIdentical($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = $call2 = new adapter\call($call1->getFunction(), array()))
			->then
				->array($calls->findIdentical(new adapter\call(uniqid())))->isEmpty()
				->array($calls->findIdentical($call1))->isIdenticalTo(array(1 => $call1, 2 => $call2))
				->array($calls->findIdentical($call2))->isIdenticalTo(array(2 => $call2))
			->if($calls[] = $call3 = new adapter\call($call1->getFunction(), array($object = new \mock\object())))
			->then
				->array($calls->findIdentical(new adapter\call(uniqid())))->isEmpty()
				->array($calls->findIdentical($call1))->isIdenticalTo(array(1 => $call1, 2 => $call2, 3 => $call3))
				->array($calls->findIdentical($call2))->isIdenticalTo(array(2 => $call2))
				->array($calls->findIdentical($call3))->isIdenticalTo(array(3 => $call3))
				->array($calls->findIdentical(new adapter\call($call1->getFunction(), array(clone $object))))->isEmpty()
			->if($calls[] = $call4 = new adapter\call($call1->getFunction(), array($object = new \mock\object(), $arg = uniqid())))
			->then
				->array($calls->findIdentical(new adapter\call(uniqid())))->isEmpty()
				->array($calls->findIdentical($call1))->isIdenticalTo(array(1 => $call1, 2 => $call2, 3 => $call3, 4 => $call4))
				->array($calls->findIdentical($call2))->isIdenticalTo(array(2 => $call2))
				->array($calls->findIdentical($call3))->isIdenticalTo(array(3 => $call3))
				->array($calls->findIdentical(new adapter\call($call1->getFunction(), array(clone $object))))->isEmpty()
				->array($calls->findIdentical($call4))->isIdenticalTo(array(4 => $call4))
				->array($calls->findIdentical(new adapter\call($call1->getFunction(), array(clone $object, $arg))))->isEmpty()
		;
	}

	public function testFind()
	{
		$this
			->if($calls = new testedClass())
			->then
				->array($calls->find(new adapter\call(uniqid())))->isEmpty()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->array($calls->find(new adapter\call(uniqid())))->isEmpty()
				->array($calls->find($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = $call2 = new adapter\call($call1->getFunction(), array()))
			->then
				->array($calls->find(new adapter\call(uniqid())))->isEmpty()
				->array($calls->find($call1))->isIdenticalTo(array(1 => $call1, 2 => $call2))
				->array($calls->find($call2))->isIdenticalTo(array(2 => $call2))
			->if($calls[] = $call3 = new adapter\call($call1->getFunction(), array($object = new \mock\object())))
			->then
				->array($calls->find(new adapter\call(uniqid())))->isEmpty()
				->array($calls->find($call1))->isIdenticalTo(array(1 => $call1, 2 => $call2, 3 => $call3))
				->array($calls->find($call2))->isIdenticalTo(array(2 => $call2))
				->array($calls->find($call3))->isIdenticalTo(array(3 => $call3))
				->array($calls->find(new adapter\call($call1->getFunction(), array(clone $object))))->isIdenticalTo(array(3 => $call3))
			->if($calls[] = $call4 = new adapter\call($call1->getFunction(), array($object = new \mock\object(), $arg = uniqid())))
			->then
				->array($calls->find(new adapter\call(uniqid())))->isEmpty()
				->array($calls->find($call1))->isIdenticalTo(array(1 => $call1, 2 => $call2, 3 => $call3, 4 => $call4))
				->array($calls->find($call2))->isIdenticalTo(array(2 => $call2))
				->array($calls->find($call3))->isIdenticalTo(array(3 => $call3, 4 => $call4))
				->array($calls->find(new adapter\call($call1->getFunction(), array(clone $object))))->isIdenticalTo(array(3 => $call3, 4 => $call4))
				->array($calls->find($call4))->isIdenticalTo(array(4 => $call4))
				->array($calls->find(new adapter\call($call1->getFunction(), array(clone $object, $arg))))->isIdenticalTo(array(4 => $call4))
			->if($calls = new testedClass())
			->then
				->array($calls->find(new adapter\call(uniqid()), true))->isEmpty()
			->if($calls[] = $call5 = new adapter\call(uniqid()))
			->then
				->array($calls->find(new adapter\call(uniqid()), true))->isEmpty()
				->array($calls->find($call5, true))->isIdenticalTo(array(5 => $call5))
			->if($calls[] = $call6 = new adapter\call($call5->getFunction(), array()))
			->then
				->array($calls->find(new adapter\call(uniqid()), true))->isEmpty()
				->array($calls->find($call5, true))->isIdenticalTo(array(5 => $call5, 6 => $call6))
				->array($calls->find($call6, true))->isIdenticalTo(array(6 => $call6))
			->if($calls[] = $call7 = new adapter\call($call5->getFunction(), array($object = new \mock\object())))
			->then
				->array($calls->find(new adapter\call(uniqid()), true))->isEmpty()
				->array($calls->find($call5, true))->isIdenticalTo(array(5 => $call5, 6 => $call6, 7 => $call7))
				->array($calls->find($call6, true))->isIdenticalTo(array(6 => $call6))
				->array($calls->find($call7, true))->isIdenticalTo(array(7 => $call7))
				->array($calls->find(new adapter\call($call5->getFunction(), array(clone $object)), true))->isEmpty()
			->if($calls[] = $call8 = new adapter\call($call5->getFunction(), array($object = new \mock\object(), $arg = uniqid())))
			->then
				->array($calls->find(new adapter\call(uniqid()), true))->isEmpty()
				->array($calls->find($call5, true))->isIdenticalTo(array(5 => $call5, 6 => $call6, 7 => $call7, 8 => $call8))
				->array($calls->find($call6, true))->isIdenticalTo(array(6 => $call6))
				->array($calls->find($call7, true))->isIdenticalTo(array(7 => $call7))
				->array($calls->find(new adapter\call($call5->getFunction(), array(clone $object)), true))->isEmpty()
				->array($calls->find($call8, true))->isIdenticalTo(array(8 => $call8))
				->array($calls->find(new adapter\call($call5->getFunction(), array(clone $object, $arg)), true))->isEmpty()
		;
	}

	public function testFindFirstEqual()
	{
		$this
			->if($calls = new testedClass())
			->then
				->variable($calls->findFirstEqual(new adapter\call(uniqid())))->isNull()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->variable($calls->findFirstEqual(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirstEqual($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = $call1)
			->then
				->variable($calls->findFirstEqual(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirstEqual($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = clone $call1)
			->then
				->variable($calls->findFirstEqual(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirstEqual($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = $call4 = new adapter\call($call1->getFunction(), array()))
			->then
				->variable($calls->findFirstEqual(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirstEqual($call1))->isIdenticalTo(array(1 => $call1))
				->array($calls->findFirstEqual($call4))->isIdenticalTo(array(4 => $call4))
			->if($calls[] = $call5 = new adapter\call($call1->getFunction(), array($object = new \mock\object())))
			->then
				->variable($calls->findFirstEqual(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirstEqual($call1))->isIdenticalTo(array(1 => $call1))
				->array($calls->findFirstEqual($call4))->isIdenticalTo(array(4 => $call4))
				->array($calls->findFirstEqual($call5))->isIdenticalTo(array(5 => $call5))
			->if($calls[] = $call6 = new adapter\call($call1->getFunction(), array(clone $object)))
			->then
				->variable($calls->findFirstEqual(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirstEqual($call1))->isIdenticalTo(array(1 => $call1))
				->array($calls->findFirstEqual($call4))->isIdenticalTo(array(4 => $call4))
				->array($calls->findFirstEqual($call5))->isIdenticalTo(array(5 => $call5))
		;
	}

	public function testFindFirstIdentical()
	{
		$this
			->if($calls = new testedClass())
			->then
				->variable($calls->findFirstIdentical(new adapter\call(uniqid())))->isNull()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->variable($calls->findFirstIdentical(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirstIdentical($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = $call1)
			->then
				->variable($calls->findFirstIdentical(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirstIdentical($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = clone $call1)
			->then
				->variable($calls->findFirstIdentical(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirstIdentical($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = $call4 = new adapter\call($call1->getFunction(), array()))
			->then
				->variable($calls->findFirstIdentical(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirstIdentical($call1))->isIdenticalTo(array(1 => $call1))
				->array($calls->findFirstIdentical($call4))->isIdenticalTo(array(4 => $call4))
			->if($calls[] = $call5 = new adapter\call($call1->getFunction(), array($object = new \mock\object())))
			->then
				->variable($calls->findFirstIdentical(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirstIdentical($call1))->isIdenticalTo(array(1 => $call1))
				->array($calls->findFirstIdentical($call4))->isIdenticalTo(array(4 => $call4))
				->array($calls->findFirstIdentical($call5))->isIdenticalTo(array(5 => $call5))
				->variable($calls->findFirstIdentical(new adapter\call($call1->getFunction(), array(clone $object))))->isNull()
			->if($calls[] = $call6 = new adapter\call($call1->getFunction(), array($clone = clone $object)))
			->then
				->variable($calls->findFirstIdentical(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirstIdentical($call1))->isIdenticalTo(array(1 => $call1))
				->array($calls->findFirstIdentical($call4))->isIdenticalTo(array(4 => $call4))
				->array($calls->findFirstIdentical($call5))->isIdenticalTo(array(5 => $call5))
				->array($calls->findFirstIdentical($call6))->isIdenticalTo(array(6 => $call6))
				->variable($calls->findFirstIdentical(new adapter\call($call1->getFunction(), array(clone $object))))->isNull()
		;
	}

	public function testFindFirst()
	{
		$this
			->if($calls = new testedClass())
			->then
				->variable($calls->findFirst(new adapter\call(uniqid())))->isNull()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->variable($calls->findFirst(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirst($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = $call1)
			->then
				->variable($calls->findFirst(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirst($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = clone $call1)
			->then
				->variable($calls->findFirst(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirst($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = $call4 = new adapter\call($call1->getFunction(), array()))
			->then
				->variable($calls->findFirst(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirst($call1))->isIdenticalTo(array(1 => $call1))
				->array($calls->findFirst($call4))->isIdenticalTo(array(4 => $call4))
			->if($calls[] = $call5 = new adapter\call($call1->getFunction(), array($object = new \mock\object())))
			->then
				->variable($calls->findFirst(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirst($call1))->isIdenticalTo(array(1 => $call1))
				->array($calls->findFirst($call4))->isIdenticalTo(array(4 => $call4))
				->array($calls->findFirst($call5))->isIdenticalTo(array(5 => $call5))
			->if($calls[] = $call6 = new adapter\call($call1->getFunction(), array(clone $object)))
			->then
				->variable($calls->findFirst(new adapter\call(uniqid())))->isNull()
				->array($calls->findFirst($call1))->isIdenticalTo(array(1 => $call1))
				->array($calls->findFirst($call4))->isIdenticalTo(array(4 => $call4))
				->array($calls->findFirst($call5))->isIdenticalTo(array(5 => $call5))
			->if($calls = new testedClass())
			->then
				->variable($calls->findFirst(new adapter\call(uniqid()), true))->isNull()
			->if($calls[] = $call7 = new adapter\call(uniqid()))
			->then
				->variable($calls->findFirst(new adapter\call(uniqid()), true))->isNull()
				->array($calls->findFirst($call7))->isIdenticalTo(array(7 => $call7))
			->if($calls[] = $call7)
			->then
				->variable($calls->findFirst(new adapter\call(uniqid()), true))->isNull()
				->array($calls->findFirst($call7, true))->isIdenticalTo(array(7 => $call7))
			->if($calls[] = clone $call7)
			->then
				->variable($calls->findFirst(new adapter\call(uniqid()), true))->isNull()
				->array($calls->findFirst($call7, true))->isIdenticalTo(array(7 => $call7))
			->if($calls[] = $call10 = new adapter\call($call7->getFunction(), array()))
			->then
				->variable($calls->findFirst(new adapter\call(uniqid()), true))->isNull()
				->array($calls->findFirst($call7, true))->isIdenticalTo(array(7 => $call7))
				->array($calls->findFirst($call10, true))->isIdenticalTo(array(10 => $call10))
			->if($calls[] = $call11 = new adapter\call($call6->getFunction(), array($object = new \mock\object())))
			->then
				->variable($calls->findFirst(new adapter\call(uniqid()), true))->isNull()
				->array($calls->findFirst($call7, true))->isIdenticalTo(array(7 => $call7))
				->array($calls->findFirst($call10, true))->isIdenticalTo(array(10 => $call10))
				->array($calls->findFirst($call11, true))->isIdenticalTo(array(11 => $call11))
				->variable($calls->findFirst(new adapter\call($call6->getFunction(), array(clone $object)), true))->isNull()
			->if($calls[] = $call12 = new adapter\call($call6->getFunction(), array($clone = clone $object)))
			->then
				->variable($calls->findFirst(new adapter\call(uniqid()), true))->isNull()
				->array($calls->findFirst($call7, true))->isIdenticalTo(array(7 => $call7))
				->array($calls->findFirst($call10, true))->isIdenticalTo(array(10 => $call10))
				->array($calls->findFirst($call11, true))->isIdenticalTo(array(11 => $call11))
				->array($calls->findFirst($call12, true))->isIdenticalTo(array(12 => $call12))
				->variable($calls->findFirst(new adapter\call($call7->getFunction(), array(clone $object)), true))->isNull()
		;
	}

	public function testFindLastEqual()
	{
		$this
			->if($calls = new testedClass())
			->then
				->variable($calls->findLastEqual(new adapter\call(uniqid())))->isNull()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->variable($calls->findLastEqual(new adapter\call(uniqid())))->isNull()
				->array($calls->findLastEqual($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = $call2 = clone $call1)
			->then
				->variable($calls->findLastEqual(new adapter\call(uniqid())))->isNull()
				->array($calls->findLastEqual($call1))->isIdenticalTo(array(2 => $call2))
			->if($calls[] = $call3 = new adapter\call($call1->getFunction(), array()))
			->then
				->variable($calls->findLastEqual(new adapter\call(uniqid())))->isNull()
				->array($calls->findLastEqual($call1))->isIdenticalTo(array(3 => $call3))
				->array($calls->findLastEqual($call3))->isIdenticalTo(array(3 => $call3))
			->if($calls[] = $call4 = clone $call3)
			->then
				->variable($calls->findLastEqual(new adapter\call(uniqid())))->isNull()
				->array($calls->findLastEqual($call1))->isIdenticalTo(array(4 => $call4))
				->array($calls->findLastEqual($call3))->isIdenticalTo(array(4 => $call4))
			->if($calls[] = $call5 = new adapter\call($call1->getFunction(), array($object = new \mock\object())))
			->then
				->variable($calls->findLastEqual(new adapter\call(uniqid())))->isNull()
				->array($calls->findLastEqual($call1))->isIdenticalTo(array(5 => $call5))
				->array($calls->findLastEqual($call3))->isIdenticalTo(array(4 => $call4))
				->array($calls->findLastEqual($call5))->isIdenticalTo(array(5 => $call5))
			->if($calls[] = $call6 = new adapter\call($call1->getFunction(), array(clone $object)))
			->then
				->variable($calls->findLastEqual(new adapter\call(uniqid())))->isNull()
				->array($calls->findLastEqual($call1))->isIdenticalTo(array(6 => $call6))
				->array($calls->findLastEqual($call3))->isIdenticalTo(array(4 => $call4))
				->array($calls->findLastEqual($call5))->isIdenticalTo(array(6 => $call6))
		;
	}

	public function testFindLastIdentical()
	{
		$this
			->if($calls = new testedClass())
			->then
				->variable($calls->findLastIdentical(new adapter\call(uniqid())))->isNull()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->variable($calls->findLastIdentical(new adapter\call(uniqid())))->isNull()
				->array($calls->findLastIdentical($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = $call2 = clone $call1)
			->then
				->variable($calls->findLastIdentical(new adapter\call(uniqid())))->isNull()
				->array($calls->findLastIdentical($call1))->isIdenticalTo(array(2 => $call2))
			->if($calls[] = $call3 = new adapter\call($call1->getFunction(), array()))
			->then
				->variable($calls->findLastIdentical(new adapter\call(uniqid())))->isNull()
				->array($calls->findLastIdentical($call1))->isIdenticalTo(array(3 => $call3))
				->array($calls->findLastIdentical($call3))->isIdenticalTo(array(3 => $call3))
			->if($calls[] = $call4 = clone $call3)
			->then
				->variable($calls->findLastIdentical(new adapter\call(uniqid())))->isNull()
				->array($calls->findLastIdentical($call1))->isIdenticalTo(array(4 => $call4))
				->array($calls->findLastIdentical($call3))->isIdenticalTo(array(4 => $call4))
			->if($calls[] = $call5 = new adapter\call($call1->getFunction(), array($object = new \mock\object())))
			->then
				->variable($calls->findLastIdentical(new adapter\call(uniqid())))->isNull()
				->array($calls->findLastIdentical($call1))->isIdenticalTo(array(5 => $call5))
				->array($calls->findLastIdentical($call3))->isIdenticalTo(array(4 => $call4))
				->array($calls->findLastIdentical($call5))->isIdenticalTo(array(5 => $call5))
				->variable($calls->findLastIdentical(new adapter\call($call1->getFunction(), array(clone $object))))->isNull()
			->if($calls[] = $call6 = new adapter\call($call1->getFunction(), array(clone $object)))
			->then
				->variable($calls->findLastIdentical(new adapter\call(uniqid())))->isNull()
				->array($calls->findLastIdentical($call1))->isIdenticalTo(array(6 => $call6))
				->array($calls->findLastIdentical($call3))->isIdenticalTo(array(4 => $call4))
				->array($calls->findLastIdentical($call5))->isIdenticalTo(array(5 => $call5))
				->array($calls->findLastIdentical($call6))->isIdenticalTo(array(6 => $call6))
				->variable($calls->findLastIdentical(new adapter\call($call1->getFunction(), array(clone $object))))->isNull()
		;
	}

	public function testFindLast()
	{
		$this
			->if($calls = new testedClass())
			->then
				->variable($calls->findLast(new adapter\call(uniqid())))->isNull()
			->if($calls[] = $call1 = new adapter\call(uniqid()))
			->then
				->variable($calls->findLast(new adapter\call(uniqid())))->isNull()
				->array($calls->findLast($call1))->isIdenticalTo(array(1 => $call1))
			->if($calls[] = $call2 = clone $call1)
			->then
				->variable($calls->findLast(new adapter\call(uniqid())))->isNull()
				->array($calls->findLast($call1))->isIdenticalTo(array(2 => $call2))
			->if($calls[] = $call3 = new adapter\call($call1->getFunction(), array()))
			->then
				->variable($calls->findLast(new adapter\call(uniqid())))->isNull()
				->array($calls->findLast($call1))->isIdenticalTo(array(3 => $call3))
				->array($calls->findLast($call3))->isIdenticalTo(array(3 => $call3))
			->if($calls[] = $call4 = clone $call3)
			->then
				->variable($calls->findLast(new adapter\call(uniqid())))->isNull()
				->array($calls->findLast($call1))->isIdenticalTo(array(4 => $call4))
				->array($calls->findLast($call3))->isIdenticalTo(array(4 => $call4))
			->if($calls[] = $call5 = new adapter\call($call1->getFunction(), array($object = new \mock\object())))
			->then
				->variable($calls->findLast(new adapter\call(uniqid())))->isNull()
				->array($calls->findLast($call1))->isIdenticalTo(array(5 => $call5))
				->array($calls->findLast($call3))->isIdenticalTo(array(4 => $call4))
				->array($calls->findLast($call5))->isIdenticalTo(array(5 => $call5))
			->if($calls[] = $call6 = new adapter\call($call1->getFunction(), array(clone $object)))
			->then
				->variable($calls->findLast(new adapter\call(uniqid())))->isNull()
				->array($calls->findLast($call1))->isIdenticalTo(array(6 => $call6))
				->array($calls->findLast($call3))->isIdenticalTo(array(4 => $call4))
				->array($calls->findLast($call5))->isIdenticalTo(array(6 => $call6))
			->if($calls = new testedClass())
			->then
				->variable($calls->findLast(new adapter\call(uniqid()), true))->isNull()
			->if($calls[] = $call7 = new adapter\call(uniqid()))
			->then
				->variable($calls->findLast(new adapter\call(uniqid()), true))->isNull()
				->array($calls->findLast($call7, true))->isIdenticalTo(array(7 => $call7))
			->if($calls[] = $call8 = clone $call7)
			->then
				->variable($calls->findLast(new adapter\call(uniqid()), true))->isNull()
				->array($calls->findLast($call7, true))->isIdenticalTo(array(8 => $call8))
			->if($calls[] = $call9 = new adapter\call($call7->getFunction(), array()))
			->then
				->variable($calls->findLast(new adapter\call(uniqid()), true))->isNull()
				->array($calls->findLast($call7, true))->isIdenticalTo(array(9 => $call9))
				->array($calls->findLast($call9, true))->isIdenticalTo(array(9 => $call9))
			->if($calls[] = $call10 = clone $call9)
			->then
				->variable($calls->findLast(new adapter\call(uniqid()), true))->isNull()
				->array($calls->findLast($call7, true))->isIdenticalTo(array(10 => $call10))
				->array($calls->findLast($call9, true))->isIdenticalTo(array(10 => $call10))
			->if($calls[] = $call11 = new adapter\call($call7->getFunction(), array($object = new \mock\object())))
			->then
				->variable($calls->findLast(new adapter\call(uniqid()), true))->isNull()
				->array($calls->findLast($call7, true))->isIdenticalTo(array(11 => $call11))
				->array($calls->findLast($call9, true))->isIdenticalTo(array(10 => $call10))
				->array($calls->findLast($call11, true))->isIdenticalTo(array(11 => $call11))
				->variable($calls->findLast(new adapter\call($call7->getFunction(), array(clone $object)), true))->isNull()
			->if($calls[] = $call12 = new adapter\call($call7->getFunction(), array(clone $object)))
			->then
				->variable($calls->findLast(new adapter\call(uniqid()), true))->isNull()
				->array($calls->findLast($call7, true))->isIdenticalTo(array(12 => $call12))
				->array($calls->findLast($call9, true))->isIdenticalTo(array(10 => $call10))
				->array($calls->findLast($call11, true))->isIdenticalTo(array(11 => $call11))
				->array($calls->findLast($call12, true))->isIdenticalTo(array(12 => $call12))
				->variable($calls->findLast(new adapter\call($call7->getFunction(), array(clone $object)), true))->isNull()
		;
	}
}
