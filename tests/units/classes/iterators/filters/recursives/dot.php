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
			->if->mockGenerator->shunt('__construct')
			->and($filter = new recursives\dot($recursiveIterator = new \mock\recursiveDirectoryIterator(uniqid())))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($recursiveIterator)
			->and($filter = new recursives\dot(__DIR__))
			->then
				->object($filter->getInnerIterator())->isEqualTo(new \recursiveDirectoryIterator(__DIR__ ))
				->string($filter->getInnerIterator()->getPath())->isEqualTo(__DIR__)
			->if($dependencies = new atoum\dependencies())
			->and($dependencies['iterator'] = function($dependencies) use (& $innerIterator) { return ($innerIterator = new \mock\recursiveDirectoryIterator($dependencies['directory']())); })
			->and($filter = new recursives\dot($path = uniqid(), $dependencies))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($innerIterator)
				->mock($filter->getInnerIterator())->call('__construct')->withArguments($path, null)->once()
			->if($dependencies['iterator']['directory'] = $otherPath = uniqid())
			->and($filter = new recursives\dot($path = uniqid(), $dependencies))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($innerIterator)
				->mock($filter->getInnerIterator())->call('__construct')->withArguments($otherPath, null)->once()
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
}
