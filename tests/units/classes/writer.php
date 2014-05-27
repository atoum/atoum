<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mock\mageekguy\atoum\writer as testedClass
;

require_once __DIR__ . '/../runner.php';

class writer extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isAbstract();
	}

	public function test__construct()
	{
		$this
			->if($writer = new testedClass())
			->then
				->object($writer->getAdapter())->isEqualTo(new atoum\adapter())
				->array($writer->getDecorators())->isEmpty()
			->if($writer = new testedClass($adapter = new atoum\test\adapter()))
			->then
				->object($writer->getAdapter())->isIdenticalTo($adapter)
				->array($writer->getDecorators())->isEmpty()
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($writer = new testedClass())
			->then
				->object($writer->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($writer)
				->object($writer->getAdapter())->isIdenticalTo($adapter)
				->object($writer->setAdapter())->isIdenticalTo($writer)
				->object($writer->getAdapter())
					->isNotIdenticalTo($adapter)
					->isEqualTo(new atoum\adapter())
		;
	}

	public function testAddDecorator()
	{
		$this
			->if($writer = new testedClass())
			->then
				->object($writer->addDecorator($decorator1 = new \mock\mageekguy\atoum\writer\decorator()))->isIdenticalTo($writer)
				->array($writer->getDecorators())->isEqualTo(array($decorator1))
				->object($writer->addDecorator($decorator1))->isIdenticalTo($writer)
				->array($writer->getDecorators())->isEqualTo(array($decorator1, $decorator1))
				->object($writer->addDecorator($decorator2 = new \mock\mageekguy\atoum\writer\decorator()))->isIdenticalTo($writer)
				->array($writer->getDecorators())->isEqualTo(array($decorator1, $decorator1, $decorator2))
		;
	}

	public function testRemoveDecorators()
	{
		$this
			->if($writer = new testedClass())
			->then
				->object($writer->removeDecorators())->isIdenticalTo($writer)
				->array($writer->getDecorators())->isEmpty()
			->if($writer->addDecorator(new \mock\mageekguy\atoum\writer\decorator()))
			->then
				->object($writer->removeDecorators())->isIdenticalTo($writer)
				->array($writer->getDecorators())->isEmpty()
		;
	}

	public function testWrite()
	{
		$this
			->if($writer = new \mock\mageekguy\atoum\writer())
			->then
				->object($writer->write($message = uniqid()))->isIdenticalTo($writer)
				->mock($writer)->call('doWrite')->withArguments($message)->once()
			->if($writer->addDecorator($decorator1 = new \mock\mageekguy\atoum\writer\decorator()))
			->and($this->calling($decorator1)->decorate = $decoratedMessage1 = uniqid())
			->then
				->object($writer->write($message = uniqid()))->isIdenticalTo($writer)
				->mock($writer)->call('doWrite')->withArguments($decoratedMessage1)->once()
			->if($writer->addDecorator($decorator2 = new \mock\mageekguy\atoum\writer\decorator()))
			->and($this->calling($decorator2)->decorate = $decoratedMessage2 = uniqid())
			->then
				->object($writer->write($message = uniqid()))->isIdenticalTo($writer)
				->mock($decorator1)->call('decorate')->withArguments($message)->once()
				->mock($decorator2)->call('decorate')->withArguments($decoratedMessage1)->once()
				->mock($writer)->call('doWrite')->withArguments($decoratedMessage2)->once()
		;
	}

	public function testReset()
	{
		$this
			->if($writer = new testedClass())
			->then
				->object($writer->reset())->isIdenticalTo($writer)
		;
	}
}
