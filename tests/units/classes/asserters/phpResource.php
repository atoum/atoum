<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\variable
;

require_once __DIR__ . '/../../runner.php';

class phpResource extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\variable');
	}

	public function test__construct(asserter\generator $generator, variable\analyzer $analyzer, atoum\locale $locale)
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()

			->given($this->newTestedInstance($generator, $analyzer, $locale))
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
			->given(
				$asserter = $this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $notAResource = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notAResource)
				->mock($locale)
					->call('_')
						->withArguments('%s is not a resource', $asserter)
						->once
				->string($asserter->getValue())->isEqualTo($value)

				->object($asserter->setWith($value = fopen(__FILE__, 'r')))->isIdenticalTo($asserter)
				->resource($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testIsOfType()
	{
		$this
			->given($asserter = $this->newTestedInstance)

			->if($asserter->setWith(fopen(__FILE__, 'r')))
			->then
				->object($asserter->isOfType('stream'))->isIdenticalTo($asserter)

			->if(
				$asserter
					->setWith($value = fopen(__FILE__, 'r'))
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $notAResource = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isOfType('foo'); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notAResource)
				->mock($locale)->call('_')->withArguments('%s is not of type %s', $asserter, 0)->once
		;
	}

	public function testCallSimpleMatch()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->if(
				$asserter->setWith(fopen(__FILE__, 'r')),
				$this->function->get_resource_type = 'foo bar'
			)
			->then
				->object($asserter->isFooBar())->isIdenticalTo($asserter);
	}

	public function testCallUnderscoreMatch()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->if(
				$asserter->setWith(fopen(__FILE__, 'r')),
				$this->function->get_resource_type = 'foo_bar'
			)
			->then
				->object($asserter->isFooBar())->isIdenticalTo($asserter);
	}

	public function testCallDotMatch()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->if(
				$asserter->setWith(fopen(__FILE__, 'r')),
				$this->function->get_resource_type = 'foo.bar'
			)
			->then
				->object($asserter->isFooBar())->isIdenticalTo($asserter);
	}

	public function testCallCamlCaseMatch()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->if(
				$asserter->setWith(fopen(__FILE__, 'r')),
				$this->function->get_resource_type = 'fooBar'
			)
			->then
				->object($asserter->isFooBar())->isIdenticalTo($asserter);
	}

	public function testTypeAsserter()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->if(
				$asserter->setWith(fopen(__FILE__, 'r')),
				$this->function->get_resource_type = 'fooBar'
			)
			->then
				->object($asserter->type->matches('/^foobar$/i'))->isInstanceOf('mageekguy\atoum\asserters\phpString');
	}
}
