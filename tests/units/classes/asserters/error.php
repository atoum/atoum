<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class error extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserter');
	}

	public function testInitWithTest()
	{
		$this
			->if($asserter = new asserters\error($generator = new asserter\generator()))
			->then
				->object($asserter->setWithTest($this))->isIdenticalTo($asserter)
				->object($asserter->getScore())->isIdenticalTo($this->getScore())
		;
	}

	public function testGetAsString()
	{
		$this
			->string(asserters\error::getAsString(E_ERROR))->isEqualTo('E_ERROR')
			->string(asserters\error::getAsString(E_WARNING))->isEqualTo('E_WARNING')
			->string(asserters\error::getAsString(E_PARSE))->isEqualTo('E_PARSE')
			->string(asserters\error::getAsString(E_NOTICE))->isEqualTo('E_NOTICE')
			->string(asserters\error::getAsString(E_CORE_ERROR))->isEqualTo('E_CORE_ERROR')
			->string(asserters\error::getAsString(E_CORE_WARNING))->isEqualTo('E_CORE_WARNING')
			->string(asserters\error::getAsString(E_COMPILE_ERROR))->isEqualTo('E_COMPILE_ERROR')
			->string(asserters\error::getAsString(E_COMPILE_WARNING))->isEqualTo('E_COMPILE_WARNING')
			->string(asserters\error::getAsString(E_USER_ERROR))->isEqualTo('E_USER_ERROR')
			->string(asserters\error::getAsString(E_USER_WARNING))->isEqualTo('E_USER_WARNING')
			->string(asserters\error::getAsString(E_USER_NOTICE))->isEqualTo('E_USER_NOTICE')
			->string(asserters\error::getAsString(E_STRICT))->isEqualTo('E_STRICT')
			->string(asserters\error::getAsString(E_RECOVERABLE_ERROR))->isEqualTo('E_RECOVERABLE_ERROR')
			->string(asserters\error::getAsString(E_DEPRECATED))->isEqualTo('E_DEPRECATED')
			->string(asserters\error::getAsString(E_USER_DEPRECATED))->isEqualTo('E_USER_DEPRECATED')
			->string(asserters\error::getAsString(E_ALL))->isEqualTo('E_ALL')
			->string(asserters\error::getAsString('unknown error'))->isEqualTo('UNKNOWN')
		;
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\error($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getMessage())->isNull()
				->variable($asserter->getType())->isNull()
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\error($generator = new asserter\generator()))
			->then
				->object($asserter->setWith(null, null))->isIdenticalTo($asserter)
				->variable($asserter->getMessage())->isNull()
				->variable($asserter->getType())->isNull()
				->object($asserter->setWith($message = uniqid(), null))->isIdenticalTo($asserter)
				->string($asserter->getMessage())->isEqualTo($message)
				->variable($asserter->getType())->isNull()
				->object($asserter->setWith($message = uniqid(), $type = rand(0, PHP_INT_MAX)))->isIdenticalTo($asserter)
				->string($asserter->getMessage())->isEqualTo($message)
				->integer($asserter->getType())->isEqualTo($type)
		;
	}

	public function testExists()
	{
		$this
			->if($asserter = new asserters\error($generator = new asserter\generator()))
			->then
				->exception(function() use (& $line, $asserter) { $asserter->exists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($generator->getLocale()->_('error does not exist'))
			->if($asserter->getScore()->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(0, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->exists())->isIdenticalTo($asserter)
				->array($asserter->getScore()->getErrors())->isEmpty()
			->if($asserter->setWith($message = uniqid(), null))
			->then
				->exception(function() use (& $line, $asserter) { $asserter->exists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('error with message \'%s\' does not exist'), $message))
			->if($asserter->getScore()->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(0, PHP_INT_MAX), $message, uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->exists())->isIdenticalTo($asserter)
				->array($asserter->getScore()->getErrors())->isEmpty()
			->if($asserter->setWith($message = uniqid(), $type = E_USER_ERROR))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('error of type %s with message \'%s\' does not exist'), asserters\error::getAsString($type), $message))
			->if($asserter->getScore()->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $type, $message, uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->exists())->isIdenticalTo($asserter)
				->array($asserter->getScore()->getErrors())->isEmpty()
			->if($asserter->setWith(null, $type = E_USER_ERROR))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('error of type %s does not exist'), asserters\error::getAsString($type)))
			->if($asserter->getScore()->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $type, uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->exists())->isIdenticalTo($asserter)
				->array($asserter->getScore()->getErrors())->isEmpty()
			->if($asserter->getScore()->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX), $message = uniqid() . 'FOO' . uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($asserter->withPattern('/FOO/')->withType(null))
			->then
				->object($asserter->exists())->isIdenticalTo($asserter)
				->array($asserter->getScore()->getErrors())->isEmpty()
		;
	}

	public function testWithType()
	{
		$this
			->if($asserter = new asserters\error(new asserter\generator()))
			->then
				->object($asserter->withType($type = rand(1, PHP_INT_MAX)))->isIdenticalTo($asserter)
				->integer($asserter->getType())->isEqualTo($type)
		;
	}

	public function testWithAnyType()
	{

		$this
			->if($asserter = new asserters\error(new asserter\generator()))
			->and($asserter->withType(rand(1, PHP_INT_MAX)))
			->then
				->variable($asserter->getType())->isNotNull()
				->object($asserter->withAnyType())->isIdenticalTo($asserter)
				->variable($asserter->getType())->isNull()
		;
	}

	public function testWithMessage()
	{
		$this
			->if($asserter = new asserters\error(new asserter\generator()))
			->then
				->object($asserter->withMessage($message = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getMessage())->isEqualTo($message)
				->boolean($asserter->messageIsPattern())->isFalse()
		;
	}

	public function testWithPattern()
	{
		$this
			->if($asserter = new asserters\error(new asserter\generator()))
			->then
				->boolean($asserter->messageIsPattern())->isFalse()
				->object($asserter->withPattern($pattern = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getMessage())->isEqualTo($pattern)
				->boolean($asserter->messageIsPattern())->isTrue()
		;
	}

	public function testWithAnyMessage()
	{
		$this
			->if($asserter = new asserters\error(new asserter\generator()))
			->and($asserter->withMessage(uniqid()))
			->then
				->variable($asserter->getMessage())->isNotNull()
				->boolean($asserter->messageIsPattern())->isFalse()
				->object($asserter->withAnyMessage())->isIdenticalTo($asserter)
				->variable($asserter->getMessage())->isNull()
				->boolean($asserter->messageIsPattern())->isFalse()
			->if($asserter->withPattern(uniqid()))
			->then
				->variable($asserter->getMessage())->isNotNull()
				->boolean($asserter->messageIsPattern())->isTrue()
				->object($asserter->withAnyMessage())->isIdenticalTo($asserter)
				->variable($asserter->getMessage())->isNull()
				->boolean($asserter->messageIsPattern())->isFalse()
		;
	}
}
