<?php

namespace mageekguy\atoum\tests\units;

require __DIR__ . '/../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\asserters,
	mock\mageekguy\atoum\asserter as sut
;

class asserter extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($asserter = new sut($generator = new atoum\asserter\generator()))
			->then
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
			->if($asserter = new sut())
			->then
				->object($generator = $asserter->getGenerator())->isEqualTo(new atoum\asserter\generator())
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
		;
	}

	public function test__get()
	{
		$this
			->if($asserter = new sut($generator = new atoum\asserter\generator()))
			->then
				->object($integerAsserter = $asserter->integer)->isEqualTo(new asserters\integer($generator))
				->object($integerAsserter->getGenerator())->isIdenticalTo($generator)
		;
	}

	public function test__call()
	{
		$this
			->if($asserter = new sut($generator = new atoum\asserter\generator()))
			->then
				->object($integerAsserter = $asserter->integer($integer = rand(1, PHP_INT_MAX)))->isInstanceOf('mageekguy\atoum\asserters\integer')
				->integer($integerAsserter->getValue())->isEqualTo($integer)
				->object($integerAsserter->getGenerator())->isIdenticalTo($generator)
		;
	}

	public function testReset()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->then
				->object($asserter->reset())->isIdenticalTo($asserter)
		;
	}

	public function testSetLocale()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->then
				->object($asserter->setLocale($locale = new atoum\locale()))->isIdenticalTo($asserter)
				->object($asserter->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testGetLocale()
	{
		$this
			->if($asserter = new sut($generator = new atoum\asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
			->if($asserter->setLocale($locale = new atoum\locale()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetGenerator()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->then
				->object($asserter->setGenerator($generator = new atoum\asserter\generator()))->isIdenticalTo($asserter)
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->setGenerator())->isIdenticalTo($asserter)
				->object($asserter->getGenerator())
					->isNotIdenticalTo($generator)
					->isEqualTo(new atoum\asserter\generator())
		;
	}

	public function testSetWithTest()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->then
				->object($asserter->setWithTest($this))->isIdenticalTo($asserter)
		;
	}

	public function testSetWithArguments()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->then
				->object($asserter->setWithArguments(array()))->isIdenticalTo($asserter)
				->mock($asserter)->call('setWith')->never()
				->object($asserter->setWithArguments(array($argument = uniqid())))->isIdenticalTo($asserter)
				->mock($asserter)->call('setWith')->withArguments($argument)->once()
		;
	}

	public function testGetTypeOf()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->and($asserter->setLocale($locale = new \mock\mageekguy\atoum\locale()))
			->then
				->string($asserter->getTypeOf(true))->isEqualTo('boolean(true)')
				->mock($locale)->call('_')->withArguments('boolean(%s)')->once()
				->string($asserter->getTypeOf(false))->isEqualTo('boolean(false)')
				->mock($locale)->call('_')->withArguments('boolean(%s)')->twice()
				->string($asserter->getTypeOf($integer = rand(1, PHP_INT_MAX)))->isEqualTo('integer(' . $integer . ')')
				->mock($locale)->call('_')->withArguments('integer(%s)')->once()
				->string($asserter->getTypeOf($float = (float) rand(1, PHP_INT_MAX)))->isEqualTo('float(' . $float . ')')
				->mock($locale)->call('_')->withArguments('float(%s)')->once()
				->string($asserter->getTypeOf(null))->isEqualTo('null')
				->mock($locale)->call('_')->withArguments('null')->once()
				->string($asserter->getTypeOf($this))->isEqualTo('object(' . get_class($this) . ')')
				->mock($locale)->call('_')->withArguments('object(%s)')->once()
				->string($asserter->getTypeOf($resource = fopen(__FILE__, 'r')))->isEqualTo('resource(' . $resource . ')')
				->mock($locale)->call('_')->withArguments('resource(%s)')->once()
				->string($asserter->getTypeOf('string'))->isEqualTo('string(6) \'string\'')
				->mock($locale)->call('_')->withArguments('string(%s) \'%s\'')->once()
				->string($asserter->getTypeOf(range(1, 10)))->isEqualTo('array(10)')
				->mock($locale)->call('_')->withArguments('array(%s)')->once()
		;
	}
}
