<?php

namespace mageekguy\atoum\tests\units\iterators\filters\recursives\atoum;

require __DIR__ . '/../../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\iterators\filters\recursives
;

class source extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\iterators\filters\recursives\dot');
	}

	public function test__accept()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($iteratorController = new mock\controller())
			->and($iteratorController->__construct = function() {})
			->and($filter = new recursives\atoum\source(new \mock\recursiveDirectoryIterator(uniqid())))
			->and($iteratorController->current = new \splFileInfo(uniqid()))
			->then
				->boolean($filter->accept())->isTrue()
			->if($iteratorController->current = new \splFileInfo('.' . uniqid()))
			->then
				->boolean($filter->accept())->isFalse()
			->if($iteratorController->current = new \splFileInfo(uniqid() . DIRECTORY_SEPARATOR . '.' . uniqid()))
			->then
				->boolean($filter->accept())->isFalse()
			->if($iteratorController->current = new \splFileInfo(uniqid() . DIRECTORY_SEPARATOR . 'GPATH'))
			->then
				->boolean($filter->accept())->isFalse()
			->if($iteratorController->current = new \splFileInfo(uniqid() . DIRECTORY_SEPARATOR . 'GRTAGS'))
			->then
				->boolean($filter->accept())->isFalse()
			->if($iteratorController->current = new \splFileInfo(uniqid() . DIRECTORY_SEPARATOR . 'GTAGS'))
			->then
				->boolean($filter->accept())->isFalse()
		;
	}
}
