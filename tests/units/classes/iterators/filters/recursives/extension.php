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
		$this->testedClass->isSubclassOf('\recursiveFilterIterator');
	}

	public function test__construct()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($iteratorController = new mock\controller())
			->and($filter = new recursives\extension($recursiveIterator = new \mock\recursiveDirectoryIterator(uniqid()), $acceptedExtensions = array('php')))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($recursiveIterator)
				->array($filter->getAcceptedExtensions())->isEqualTo($acceptedExtensions)
			->if($factory = new atoum\factory())
			->and($factory->setBuilder('recursiveDirectoryIterator', function($path) use (& $innerIterator) { return ($innerIterator = new \mock\recursiveDirectoryIterator($path)); }))
			->and($filterController = new mock\controller())
			->and($filterController->createFactory = $factory)
			->and($filter = new \mock\mageekguy\atoum\iterators\filters\recursives\extension($path = uniqid(), $acceptedExtensions = array('php')))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($innerIterator)
				->mock($filter->getInnerIterator())->call('__construct')->withArguments($path)->once()
				->array($filter->getAcceptedExtensions())->isEqualTo($acceptedExtensions)
		;
	}

	public function testAccept()
	{
		$this
			->if($filter = new recursives\extension($innerIterator = new \mock\recursiveIterator(), array('php')))
			->and($innerIterator->getMockController()->current = uniqid() . '.php')
			->then
				->boolean($filter->accept())->isTrue()
			->if($innerIterator->getMockController()->current = uniqid() . DIRECTORY_SEPARATOR . uniqid() . '.php')
				->boolean($filter->accept())->isTrue()
			->if($innerIterator->getMockController()->current = uniqid())
				->boolean($filter->accept())->isTrue()
			->if($innerIterator->getMockController()->current = uniqid() . '.' . uniqid())
				->boolean($filter->accept())->isFalse()
		;
	}
}
