<?php

namespace mageekguy\atoum\tests\units\iterators\filters\recursives;

require __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\iterators\filters\recursives
;

class extension extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('\recursiveFilterIterator');
	}

	public function test__construct()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($filter = new recursives\extension($recursiveIterator = new \mock\recursiveDirectoryIterator(uniqid()), $acceptedExtensions = array('php')))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($recursiveIterator)
				->array($filter->getAcceptedExtensions())->isEqualTo($acceptedExtensions)
			->if($filter = new recursives\extension(__DIR__, $acceptedExtensions))
			->then
				->object($filter->getInnerIterator())->isEqualTo(new \recursiveDirectoryIterator(__DIR__ ))
				->string($filter->getInnerIterator()->getPath())->isEqualTo(__DIR__)
			->if($filter = new recursives\extension($path = uniqid(), $acceptedExtensions, function($path) use (& $innerIterator) { return ($innerIterator = new \mock\recursiveDirectoryIterator($path)); }))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($innerIterator)
				->mock($filter->getInnerIterator())->call('__construct')->withArguments($path)->once()
				->array($filter->getAcceptedExtensions())->isEqualTo($acceptedExtensions)
		;
	}

	public function testAccept()
	{
		$this
			->given(
				$filter = new recursives\extension($innerIterator = new \mock\recursiveIterator(), array('php')),
				$current = new \mock\splFileInfo(uniqid())
			)
			->if(
				$this->calling($current)->getExtension = 'php',
				$this->calling($current)->isDIr = false,
				$innerIterator->getMockController()->current = $current
			)
			->then
				->boolean($filter->accept())->isTrue()
			->given($current = new \mock\splFileInfo(uniqid()))
			->if(
				$innerIterator->getMockController()->getExtension = uniqid(),
				$this->calling($current)->isDIr = false,
				$innerIterator->getMockController()->current = $current
			)
			->then
				->boolean($filter->accept())->isFalse()
			->given($current = new \mock\splFileInfo(uniqid()))
			->if(
				$innerIterator->getMockController()->getExtension = null,
				$this->calling($current)->isDIr = false,
				$innerIterator->getMockController()->current = $current
			)
			->then
				->boolean($filter->accept())->isFalse()
			->given($current = new \mock\splFileInfo(uniqid()))
			->if(
				$this->calling($current)->isDIr = true,
				$innerIterator->getMockController()->current = $current
			)
			->then
				->boolean($filter->accept())->isTrue()

		;
	}
}
