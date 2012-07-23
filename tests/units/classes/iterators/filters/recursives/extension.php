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
			->if->mockGenerator->shunt('__construct')
			->and($filter = new recursives\extension($recursiveIterator = new \mock\recursiveDirectoryIterator(uniqid()), $acceptedExtensions = array('php')))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($recursiveIterator)
				->array($filter->getAcceptedExtensions())->isEqualTo($acceptedExtensions)
			->and($filter = new recursives\extension(__DIR__, $acceptedExtensions))
			->then
				->object($filter->getInnerIterator())->isEqualTo(new \recursiveDirectoryIterator(__DIR__ ))
				->string($filter->getInnerIterator()->getPath())->isEqualTo(__DIR__)
			->if($dependencies = new atoum\dependencies())
			->and($dependencies['iterator'] = function($dependencies) use (& $innerIterator) { return ($innerIterator = new \mock\recursiveDirectoryIterator($dependencies['directory']())); })
			->and($filter = new recursives\extension($path = uniqid(), $acceptedExtensions, $dependencies))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($innerIterator)
				->mock($filter->getInnerIterator())->call('__construct')->withArguments($path)->once()
				->array($filter->getAcceptedExtensions())->isEqualTo($acceptedExtensions)
			->if($dependencies['iterator']['directory'] = $otherPath = uniqid())
			->and($filter = new recursives\extension($path = uniqid(), $acceptedExtensions, $dependencies))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($innerIterator)
				->mock($filter->getInnerIterator())->call('__construct')->withArguments($otherPath, null)->once()
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
