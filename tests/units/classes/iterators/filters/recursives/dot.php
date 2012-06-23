<?php

namespace mageekguy\atoum\tests\units\iterators\filters\recursives;

require __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\iterators\filters\recursives
;

class dot extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('\recursiveFilterIterator');
	}

	public function test__construct()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($iteratorController = new mock\controller())
			->and($filter = new recursives\dot($recursiveIterator = new \mock\recursiveDirectoryIterator(uniqid())))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($recursiveIterator)
			->if($factory = new atoum\factory())
			->and($factory->setBuilder('recursiveDirectoryIterator', function($path) use (& $innerIterator) { return ($innerIterator = new \mock\recursiveDirectoryIterator($path)); }))
			->and($filterController = new mock\controller())
			->and($filterController->createFactory = $factory)
			->and($filter = new \mock\mageekguy\atoum\iterators\filters\recursives\dot($path = uniqid()))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($innerIterator)
				->mock($filter->getInnerIterator())->call('__construct')->withArguments($path)->once()
		;
	}

	public function test__accept()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($iteratorController = new mock\controller())
			->and($iteratorController->__construct = function() {})
			->and($filter = new recursives\dot(new \mock\recursiveDirectoryIterator(uniqid())))
			->and($iteratorController->current = new \splFileInfo(uniqid()))
			->then
				->boolean($filter->accept())->isTrue()
			->if($iteratorController->current = new \splFileInfo('.' . uniqid()))
			->then
				->boolean($filter->accept())->isFalse()
			->if($iteratorController->current = new \splFileInfo(uniqid() . DIRECTORY_SEPARATOR . '.' . uniqid()))
			->then
				->boolean($filter->accept())->isFalse()
		;
	}

	public function testCreateFactory()
	{
		$this
			->if($filter = new recursives\dot(new \mock\recursiveIterator()))
			->then
				->object($filter->createFactory())->isInstanceOf('mageekguy\atoum\factory')
		;
	}
}
