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
			->if($factory = new atoum\factory())
			->and($factory->setBuilder('recursiveDirectoryIterator', function($path) use (& $innerIterator) { return ($innerIterator = new \mock\recursiveDirectoryIterator($path)); }))
			->and($filterController = new mock\controller())
			->and($filterController->createFactory = $factory)
			->and($filter = new \mock\mageekguy\atoum\iterators\filters\recursives\extension($path = uniqid(), $acceptedExtensions = array('php')))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($innerIterator)
				->mock($filter->getInnerIterator())->call('__construct')->withArguments($path)
		;
	}
}

?>
