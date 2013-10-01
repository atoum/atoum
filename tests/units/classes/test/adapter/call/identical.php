<?php

namespace mageekguy\atoum\tests\units\test\adapter\call;

require_once __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\test\adapter\call\identical as testedClass
;

class identical extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\test\adapter\call');
	}

	public function testFind()
	{
		$this
			->if($adapter = new adapter())
			->and($call = new testedClass())
			->then
				->array($call->find($adapter))->isEmpty()
			->if($call = new testedClass(uniqid()))
			->then
				->array($call->find($adapter))->isEmpty()
			->if($call = new testedClass(uniqid(), array()))
			->then
				->array($call->find($adapter))->isEmpty()
			->if($call = new testedClass(uniqid(), array(uniqid())))
			->then
				->array($call->find($adapter))->isEmpty()
			->if($adapter->addCall($function = uniqid(), $arguments = array(uniqid(), uniqid())))
			->then
				->array($call->find($adapter))->isEmpty()
			->if($call = new testedClass(uniqid()))
			->then
				->array($call->find($adapter))->isEmpty()
			->if($call = new testedClass(uniqid(), array()))
			->then
				->array($call->find($adapter))->isEmpty()
			->if($call = new testedClass(uniqid(), array(uniqid())))
			->then
				->array($call->find($adapter))->isEmpty()
			->if($call = new testedClass($function))
			->then
				->array($call->find($adapter))->isEqualTo(array(1 => $arguments))
			->if($call = new testedClass($function, $arguments))
			->then
				->array($call->find($adapter))->isEqualTo(array(1 => $arguments))
			->if($call = new testedClass($function, array()))
			->then
				->array($call->find($adapter))->isEmpty()
			->if($adapter->addCall($otherFunction = uniqid(), $otherArguments = array($object = new \mock\object())))
			->and($call = new testedClass($otherFunction))
			->then
				->array($call->find($adapter))->isEqualTo(array(2 => $otherArguments))
			->if($call = new testedClass($otherFunction, array($object)))
			->then
				->array($call->find($adapter))->isEqualTo(array(2 => $otherArguments))
			->if($call = new testedClass($otherFunction, array(clone $object)))
			->then
				->array($call->find($adapter))->isEmpty()
		;
	}
}
