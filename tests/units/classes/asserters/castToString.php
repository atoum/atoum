<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class castToString extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\string');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\castToString($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\castToString($generator = new asserter\generator()))
			->then
				->assert('Set the asserter with something else than an object throw an exception')
					->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s is not an object'), $asserter->getTypeOf($value)))
					->integer($asserter->getValue())->isEqualTo($value)
					->variable($asserter->getCharlist())->isNull()
				->assert('The asserter was returned when it set with an object')
					->object($asserter->setWith($object = new \exception()))->isIdenticalTo($asserter)
					->string($asserter->getValue())->isEqualTo((string) $object)
					->variable($asserter->getCharlist())->isNull()
				->assert('It is possible to define a character list')
					->object($asserter->setWith($object = new \exception, null, $charlist = "\010"))->isIdenticalTo($asserter)
					->string($asserter->getValue())->isEqualTo((string) $object)
					->string($asserter->getCharlist())->isEqualTo($charlist)
		;
	}

	public function testToString()
	{
		$this
			->if($asserter = new asserters\castToString(new asserter\generator()))
			->and($asserter->setWith($object = new \exception()))
			->then
				->castToString($asserter)->isEqualTo('string(' . strlen(($string = (string) $object)) . ') \'' . $string . '\'')
		;
	}
}
