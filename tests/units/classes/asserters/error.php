<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\variable
;

require_once __DIR__ . '/../../runner.php';

class error extends atoum\test
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
				->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->object($this->testedInstance->getScore())->isInstanceOf('mageekguy\atoum\test\score')
				->variable($this->testedInstance->getMessage())->isNull()
				->variable($this->testedInstance->getType())->isNull()

			->given($this->newTestedInstance($generator = new asserter\generator(), $score = new atoum\test\score(), $locale = new atoum\locale()))
			->then
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
				->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
				->object($this->testedInstance->getScore())->isIdenticalTo($score)
				->variable($this->testedInstance->getMessage())->isNull()
				->variable($this->testedInstance->getType())->isNull()
		;
	}

	public function testInitWithTest()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setWithTest($this))->isTestedInstance
				->object($this->testedInstance->getScore())->isIdenticalTo($this->getScore())
		;
	}

	public function testGetAsString()
	{
		$this
			->string(atoum\asserters\error::getAsString(E_ERROR))->isEqualTo('E_ERROR')
			->string(atoum\asserters\error::getAsString(E_WARNING))->isEqualTo('E_WARNING')
			->string(atoum\asserters\error::getAsString(E_PARSE))->isEqualTo('E_PARSE')
			->string(atoum\asserters\error::getAsString(E_NOTICE))->isEqualTo('E_NOTICE')
			->string(atoum\asserters\error::getAsString(E_CORE_ERROR))->isEqualTo('E_CORE_ERROR')
			->string(atoum\asserters\error::getAsString(E_CORE_WARNING))->isEqualTo('E_CORE_WARNING')
			->string(atoum\asserters\error::getAsString(E_COMPILE_ERROR))->isEqualTo('E_COMPILE_ERROR')
			->string(atoum\asserters\error::getAsString(E_COMPILE_WARNING))->isEqualTo('E_COMPILE_WARNING')
			->string(atoum\asserters\error::getAsString(E_USER_ERROR))->isEqualTo('E_USER_ERROR')
			->string(atoum\asserters\error::getAsString(E_USER_WARNING))->isEqualTo('E_USER_WARNING')
			->string(atoum\asserters\error::getAsString(E_USER_NOTICE))->isEqualTo('E_USER_NOTICE')
			->string(atoum\asserters\error::getAsString(E_STRICT))->isEqualTo('E_STRICT')
			->string(atoum\asserters\error::getAsString(E_RECOVERABLE_ERROR))->isEqualTo('E_RECOVERABLE_ERROR')
			->string(atoum\asserters\error::getAsString(E_DEPRECATED))->isEqualTo('E_DEPRECATED')
			->string(atoum\asserters\error::getAsString(E_USER_DEPRECATED))->isEqualTo('E_USER_DEPRECATED')
			->string(atoum\asserters\error::getAsString(E_ALL))->isEqualTo('E_ALL')
			->string(atoum\asserters\error::getAsString('unknown error'))->isEqualTo('UNKNOWN')
		;
	}

	public function testSetWith()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setWith(null, null))->isTestedInstance
				->variable($this->testedInstance->getMessage())->isNull()
				->variable($this->testedInstance->getType())->isNull()

				->object($this->testedInstance->setWith($message = uniqid(), null))->isTestedInstance
				->string($this->testedInstance->getMessage())->isEqualTo($message)
				->variable($this->testedInstance->getType())->isNull()

				->object($this->testedInstance->setWith($message = uniqid(), $type = rand(0, PHP_INT_MAX)))->isTestedInstance
				->string($this->testedInstance->getMessage())->isEqualTo($message)
				->integer($this->testedInstance->getType())->isEqualTo($type)
		;
	}

	public function testExists()
	{
		$this
			->given($asserter = $this->newTestedInstance)

			->if(
				$asserter->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $errorNotExists = uniqid()
			)
			->then
				->exception(function() use (& $line, $asserter) { $asserter->exists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorNotExists)
				->mock($locale)->call('_')->withArguments('error does not exist')->once
				->exception(function() use (& $line, $asserter) { $asserter->exists; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorNotExists)
				->mock($locale)->call('_')->withArguments('error does not exist')->twice

			->if($asserter->getScore()->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), rand(0, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->exists())->isTestedInstance
				->array($asserter->getScore()->getErrors())->isEmpty()

			->if($asserter->getScore()->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), rand(0, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->exists)->isTestedInstance
				->array($asserter->getScore()->getErrors())->isEmpty()

			->if($asserter->setWith($message = uniqid(), null))
			->then
				->exception(function() use (& $line, $asserter) { $asserter->exists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorNotExists)
				->mock($locale)->call('_')->withArguments('error with message \'%s\' does not exist', $message)->once

			->if($asserter->getScore()->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), rand(0, PHP_INT_MAX), $message, uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->exists())->isTestedInstance
				->array($asserter->getScore()->getErrors())->isEmpty()

			->if($asserter->setWith($message = uniqid(), $type = E_USER_ERROR))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorNotExists)
				->mock($locale)->call('_')->withArguments('error of type %s with message \'%s\' does not exist', atoum\asserters\error::getAsString($type), $message)->once

			->if($asserter->getScore()->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), $type, $message, uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->exists())->isTestedInstance
				->array($asserter->getScore()->getErrors())->isEmpty()

			->if($asserter->setWith(null, $type = E_USER_ERROR))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorNotExists)
				->mock($locale)->call('_')->withArguments('error of type %s does not exist', atoum\asserters\error::getAsString($type))->once

			->if($asserter->getScore()->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), $type, uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->exists())->isTestedInstance
				->array($asserter->getScore()->getErrors())->isEmpty()

			->if($asserter->getScore()->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), rand(1, PHP_INT_MAX), $message = uniqid() . 'FOO' . uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($asserter->withPattern('/FOO/')->withType(null))
			->then
				->object($asserter->exists())->isTestedInstance
				->array($asserter->getScore()->getErrors())->isEmpty()
		;
	}

	public function testNotExists()
	{
		$this
			->given($asserter = $this->newTestedInstance)

			->if(
				$asserter->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $errorExists = uniqid()
			)
			->then
				->object($asserter->notExists())->isTestedInstance
				->object($asserter->notExists)->isTestedInstance
				->array($asserter->getScore()->getErrors())->isEmpty()

			->if($asserter->getScore()->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), rand(0, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->exception(function() use (& $line, $asserter) { $asserter->notExists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorExists)
				->mock($locale)->call('_')->withArguments('error exists')->once
				->exception(function() use (& $line, $asserter) { $asserter->notExists; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorExists)
				->mock($locale)->call('_')->withArguments('error exists')->twice

			->if($asserter->setWith($message = uniqid(), null))
			->then
				->exception(function() use (& $line, $asserter) { $asserter->exists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorExists)
				->mock($locale)->call('_')->withArguments('error with message \'%s\' does not exist', $message)->once

			->if($asserter->getScore()->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), rand(0, PHP_INT_MAX), $message, uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->exception(function() use (& $line, $asserter) { $asserter->notExists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorExists)
				->mock($locale)->call('_')->withArguments('error with message \'%s\' exists')->once
				->exception(function() use (& $line, $asserter) { $asserter->notExists; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorExists)
				->mock($locale)->call('_')->withArguments('error with message \'%s\' exists')->twice
				->array($asserter->getScore()->getErrors())->isNotEmpty()

			->if($asserter->setWith($message = uniqid(), $type = E_USER_ERROR))
			->then
				->object($asserter->notExists())->isTestedInstance
				->object($asserter->notExists)->isTestedInstance
				->array($asserter->getScore()->getErrors())->isNotEmpty()

			->if($asserter->getScore()->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), $type, $message, uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->notExists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorExists)
				->mock($locale)->call('_')->withArguments('error of type %s with message \'%s\' exists', atoum\asserters\error::getAsString($type), $message)->once

			->if($asserter->setWith(null, $type = E_USER_ERROR))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->notExists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorExists)
				->mock($locale)->call('_')->withArguments('error of type %s exists', atoum\asserters\error::getAsString($type))->once

			->if($asserter->getScore()->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), $type, uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->exception(function() use (& $line, $asserter) { $asserter->notExists(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorExists)
				->mock($locale)->call('_')->withArguments('error of type %s with message \'%s\' exists')->once
				->array($asserter->getScore()->getErrors())->isNotEmpty()

			->if($asserter->getScore()->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), $type, uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->exception(function() use (& $line, $asserter) { $asserter->notExists; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($errorExists)
				->mock($locale)->call('_')->withArguments('error of type %s with message \'%s\' exists')->once
				->array($asserter->getScore()->getErrors())->isNotEmpty()

			->if($asserter->getScore()->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), rand(1, PHP_INT_MAX), $message = uniqid() . 'FOO' . uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($asserter->withPattern('/FOO/')->withType(null))
			->then
				->object($asserter->exists())->isTestedInstance
				->array($asserter->getScore()->getErrors())->isNotEmpty()
		;
	}


	public function testWithType()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->withType($type = rand(1, PHP_INT_MAX)))->isTestedInstance
				->integer($this->testedInstance->getType())->isEqualTo($type)
		;
	}

	public function testWithAnyType()
	{
		$this
			->given($this->newTestedInstance->withType(rand(1, PHP_INT_MAX)))
			->then
				->object($this->testedInstance->withAnyType())->isTestedInstance
				->variable($this->testedInstance->getType())->isNull()
			->given($this->newTestedInstance->withType(rand(1, PHP_INT_MAX)))
			->then
				->object($this->testedInstance->withAnyType)->isTestedInstance
				->variable($this->testedInstance->getType())->isNull()
		;
	}

	public function testWithMessage()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->withMessage($message = uniqid()))->isTestedInstance
				->string($this->testedInstance->getMessage())->isEqualTo($message)
				->boolean($this->testedInstance->messageIsPattern())->isFalse()
		;
	}

	public function testWithPattern()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->withPattern($pattern = uniqid()))->isTestedInstance
				->string($this->testedInstance->getMessage())->isEqualTo($pattern)
				->boolean($this->testedInstance->messageIsPattern())->isTrue()
		;
	}

	public function testWithAnyMessage()
	{
		$this
			->given($this->newTestedInstance->withMessage(uniqid()))
			->then
				->object($this->testedInstance->withAnyMessage())->isTestedInstance
				->variable($this->testedInstance->getMessage())->isNull()
				->boolean($this->testedInstance->messageIsPattern())->isFalse()

			->given($this->newTestedInstance->withMessage(uniqid()))
			->then
				->object($this->testedInstance->withAnyMessage)->isTestedInstance
				->variable($this->testedInstance->getMessage())->isNull()
				->boolean($this->testedInstance->messageIsPattern())->isFalse()

			->if($this->testedInstance->withPattern(uniqid()))
			->then
				->object($this->testedInstance->withAnyMessage())->isTestedInstance
				->variable($this->testedInstance->getMessage())->isNull()
				->boolean($this->testedInstance->messageIsPattern())->isFalse()
		;
	}

	public function testSetScore()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->setScore($score = new atoum\test\score()))->isTestedInstance
				->object($this->testedInstance->getScore())->isIdenticalTo($score)
				->object($this->testedInstance->setScore())->isTestedInstance
				->object($this->testedInstance->getScore())
					->isNotIdenticalTo($score)
					->isInstanceOf('mageekguy\atoum\score')
		;
	}
}
