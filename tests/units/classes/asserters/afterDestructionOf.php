<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class classWithDestructor
{
	public function __destruct() {}
}

class classWithoutDestructor {}

class afterDestructionOf extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\afterDestructionOf($generator = new asserter\generator()))
			->then
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\afterDestructionOf($generator = new asserter\generator()))
			->and($value = uniqid())
			->then
				->exception(function() use (& $line, $asserter, $value) { $line = __LINE__; $asserter->setWith($value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not an object'), $asserter->getTypeOf($value)))
			->object($asserter->setWith($objectWithDestructor = new \mock\mageekguy\atoum\tests\units\asserters\classWithDestructor()))->isIdenticalTo($asserter)
				->mock($objectWithDestructor)
						->call('__destruct')->once()
			->if($objectWithoutDestructor = new classWithoutDestructor())
			->then
				->exception(function() use (& $otherLine, $asserter, $objectWithoutDestructor) { $otherLine = __LINE__; $asserter->setWith($objectWithoutDestructor); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Destructor of class %s is undefined'), get_class($objectWithoutDestructor)))
		;
	}
}
