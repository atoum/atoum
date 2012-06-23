<?php

namespace atoum\tests\units\asserters;

use
	atoum,
	atoum\asserter,
	atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class exception extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\exception($generator = new asserter\generator()))
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
			->if($asserter = new asserters\exception($generator = new asserter\generator()))
			->then
				->assert('It is impossible to set asserter with something else than an exception')
					->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = uniqid()); })
						->isInstanceOf('atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s is not an exception'), $asserter->getTypeOf($value)))
					->string($asserter->getValue())->isEqualTo($value)
				->assert('The asserter was returned when it set with an exception')
					->object($asserter->setWith($value = new \exception()))->isIdenticalTo($asserter)
					->exception($asserter->getValue())->isIdenticalTo($value)
		;
	}

	public function testIsInstanceOf()
	{
		$this
			->if($asserter = new asserters\exception($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Exception is undefined')
			->if($asserter->setWith(new \exception()))
			->then
				->object($asserter->isInstanceOf('\Exception'))->isIdenticalTo($asserter)
				->object($asserter->isInstanceOf('Exception'))->isIdenticalTo($asserter)
				->object($asserter->isInstanceOf('\exception'))->isIdenticalTo($asserter)
				->object($asserter->isInstanceOf('exception'))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->isInstanceOf(uniqid()); })
					->isInstanceOf('atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument of atoum\asserters\exception::isInstanceOf() must be a \exception instance or an exception class name')
				->exception(function() use ($asserter) { $asserter->isInstanceOf('atoum\exceptions\runtime'); })
					->isInstanceOf('atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not an instance of atoum\exceptions\runtime'), $asserter))
		;
	}

	public function testHasCode()
	{
		$this
			->if($asserter = new asserters\exception($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->hasCode(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Exception is undefined')
			->if($asserter->setWith(new atoum\exceptions\runtime(uniqid(), $code = rand(2, PHP_INT_MAX))))
			->then
				->exception(function() use ($asserter, & $otherCode) { $asserter->hasCode($otherCode = 1); })
					->isInstanceOf('atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('code is %d instead of %d'), $code, $otherCode))
			->object($asserter->hasCode($code))->isIdenticalTo($asserter)
		;
	}

	public function testHasMessage()
	{
		$this
			->if($asserter = new asserters\exception($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->hasMessage(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Exception is undefined')
			->if($asserter->setWith(new atoum\exceptions\runtime($message = uniqid())))
			->then
				->exception(function() use ($asserter, & $otherMessage) { $asserter->hasMessage($otherMessage = uniqid()); })
					->isInstanceOf('atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('message \'%s\' is not identical to \'%s\''), $message, $otherMessage))
				->object($asserter->hasMessage($message))->isIdenticalTo($asserter)
		;
	}

	public function testHasNestedException()
	{
		$this
			->if($asserter = new asserters\exception($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->hasNestedException(); })
					->isInstanceOf('logicException')
					->hasMessage('Exception is undefined')
			->if($asserter->setWith(new atoum\exceptions\runtime('', 0)))
			->then
				->exception(function() use ($asserter) { $asserter->hasNestedException(); })
					->isInstanceOf('atoum\asserter\exception')
					->hasMessage($generator->getLocale()->_('exception does not contain any nested exception'))
				->exception(function() use ($asserter) { $asserter->hasNestedException(new \exception()); })
					->isInstanceOf('atoum\asserter\exception')
					->hasMessage($generator->getLocale()->_('exception does not contain this nested exception'))
			->if($asserter->setWith(new atoum\exceptions\runtime('', 0, $nestedException = new \exception())))
			->then
				->object($asserter->hasNestedException())->isIdenticalTo($asserter)
				->object($asserter->hasNestedException($nestedException))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->hasNestedException(new \exception()); })
					->isInstanceOf('atoum\asserter\exception')
					->hasMessage($generator->getLocale()->_('exception does not contain this nested exception'))
		;
	}
}
