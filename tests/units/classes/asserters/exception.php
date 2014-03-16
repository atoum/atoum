<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\variable,
	mageekguy\atoum\asserters\exception as sut
;

require_once __DIR__ . '/../../runner.php';

class exception extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->getGenerator())->isEqualTo(new atoum\asserter\generator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()

			->given($this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
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
				->object($asserter->setWith($value = new \exception()))->isIdenticalTo($asserter)
				->exception($asserter->getValue())->isIdenticalTo($value)

			->if(
				$this->calling($locale)->_ = $notAnException = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notAnException)
				->mock($locale)->call('_')->withArguments('%s is not an exception', $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments($value)->once
				->string($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testIsInstanceOf()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
			)
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Exception is undefined')

			->if($asserter->setWith(new \exception()))
			->then
				->object($asserter->isInstanceOf('\Exception'))->isIdenticalTo($asserter)
				->object($asserter->isInstanceOf('Exception'))->isIdenticalTo($asserter)
				->object($asserter->isInstanceOf('\exception'))->isIdenticalTo($asserter)
				->object($asserter->isInstanceOf('exception'))->isIdenticalTo($asserter)

				->exception(function() use ($asserter) { $asserter->isInstanceOf(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument of mageekguy\atoum\asserters\exception::isInstanceOf() must be a \exception instance or an exception class name')

			->if($this->calling($locale)->_ = $isNotAnInstance = uniqid())
			->then
				->exception(function() use ($asserter) { $asserter->isInstanceOf('mageekguy\atoum\exceptions\runtime'); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($isNotAnInstance)
				->mock($locale)->call('_')->withArguments('%s is not an instance of %s', $asserter)->once
		;
	}

	public function testHasCode()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
			)
			->then
				->exception(function() use ($asserter) { $asserter->hasCode(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Exception is undefined')

			->if($asserter->setWith(new atoum\exceptions\runtime(uniqid(), $code = rand(2, PHP_INT_MAX))))
			->then
				->object($asserter->hasCode($code))->isIdenticalTo($asserter)

			->if($this->calling($locale)->_ = $hasNotCode = uniqid())
			->then
				->exception(function() use ($asserter, & $badCode) { $asserter->hasCode($badCode = 1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($hasNotCode)
				->mock($locale)->call('_')->withArguments('code is %s instead of %s', $code, $badCode)->once
		;
	}

	public function testHasMessage()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
			)
			->then
				->exception(function() use ($asserter) { $asserter->hasMessage(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Exception is undefined')

			->if($asserter->setWith(new atoum\exceptions\runtime($message = uniqid())))
			->then
				->object($asserter->hasMessage($message))->isIdenticalTo($asserter)

			->if($this->calling($locale)->_ = $hasNotMessage = uniqid())
			->then
				->exception(function() use ($asserter, & $badMessage) { $asserter->hasMessage($badMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($hasNotMessage)
				->mock($locale)->call('_')->withArguments('message \'%s\' is not identical to \'%s\'', $message, $badMessage)->once
		;
	}

	public function testHasNestedException()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->hasNestedException(); })
					->isInstanceOf('logicException')
					->hasMessage('Exception is undefined')
			->if($asserter->setWith(new atoum\exceptions\runtime('', 0)))
			->then
				->exception(function() use ($asserter) { $asserter->hasNestedException(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($generator->getLocale()->_('exception does not contain any nested exception'))
				->exception(function() use ($asserter) { $asserter->hasNestedException(new \exception()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($generator->getLocale()->_('exception does not contain this nested exception'))
			->if($asserter->setWith(new atoum\exceptions\runtime('', 0, $nestedException = new \exception())))
			->then
				->object($asserter->hasNestedException())->isIdenticalTo($asserter)
				->object($asserter->hasNestedException($nestedException))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->hasNestedException(new \exception()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($generator->getLocale()->_('exception does not contain this nested exception'))
		;
	}

	public function testMessage()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->message; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Exception is undefined')
			->if($asserter->setWith(new atoum\exceptions\runtime('', 0)))
			->then
				->object($string = $asserter->message)
					->isInstanceOf('mageekguy\atoum\asserters\string')
				->string($string->getValue())
					->isEqualTo('')
			->if($asserter->setWith(new atoum\exceptions\runtime('Exception message', 0)))
			->then
				->object($string = $asserter->message)
					->isInstanceOf('mageekguy\atoum\asserters\string')
				->string($string->getValue())
					->isEqualTo('Exception message')
		;
	}

	public function testGetLastValue()
	{
		$this
			->variable(sut::getLastValue())->isNull()
			->if($asserter = new sut(new asserter\generator()))
			->and($asserter->setWith(function() use (& $exception) { $exception = new \exception(); throw $exception; }))
			->then
				->object(sut::getLastValue())->isIdenticalTo($exception)
			->and($asserter->setWith(function() use (& $otherException) { $otherException = new \exception(); throw $otherException; }))
			->then
				->object(sut::getLastValue())->isIdenticalTo($otherException)
		;
	}
}
