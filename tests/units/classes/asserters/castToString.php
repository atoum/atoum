<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\variable
;

require_once __DIR__ . '/../../runner.php';

class castToString extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\phpString');
	}

	public function test__construct()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()

			->given($this->newTestedInstance($generator = new atoum\asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
			->then
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->object($this->testedInstance->getAnalyzer())->isIdenticalTo($analyzer)
				->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->object($asserter->setWith($object = new \exception()))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo((string) $object)
				->variable($asserter->getCharlist())->isNull()

				->object($asserter->setWith($object = new \exception(), $charlist = "\010"))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo((string) $object)
				->string($asserter->getCharlist())->isEqualTo($charlist)

			->if(
				$this->calling($locale)->_ = $notAnObject = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notAnObject)
					->integer($asserter->getValue())->isEqualTo($value)
					->variable($asserter->getCharlist())->isNull()
				->mock($locale)->call('_')->withArguments('%s is not an object', $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments($value)->once
		;
	}

	public function testToString()
	{
		$this
			->given($asserter = $this->newTestedInstance(new asserter\generator()))

			->if($asserter->setWith($object = new \exception()))
			->then
				->castToString($asserter)->isEqualTo('string(' . strlen(($string = (string) $object)) . ') \'' . $string . '\'')
		;
	}
}
