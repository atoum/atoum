<?php

namespace
{
	if (version_compare(PHP_VERSION, '7.0.0') < 0)
	{
		class throwable {}
	}
}

namespace mageekguy\atoum\tests\units\asserters
{
	use
		mageekguy\atoum,
		mageekguy\atoum\asserter,
		mageekguy\atoum\asserters,
		mageekguy\atoum\tools\variable
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
					->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
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
				->given($this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
				)
				->then
					->object($this->testedInstance->setWith($value = new \exception()))->isTestedInstance
					->exception($this->testedInstance->getValue())->isIdenticalTo($value)

				->if(
					$this->calling($locale)->_ = $notAnException = uniqid(),
					$this->calling($analyzer)->getTypeOf = $type = uniqid()
				)
				->then
					->exception(function($test) use (& $line, & $value) { $line = __LINE__; $test->testedInstance->setWith($value = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($notAnException)
					->mock($locale)->call('_')->withArguments('%s is not an exception', $type)->once
					->mock($analyzer)->call('getTypeOf')->withArguments($value)->once
					->string($this->testedInstance->getValue())->isEqualTo($value)
			;
		}

		/** @php < 7.0.0 */
		public function testSetWithPhpLt7(atoum\locale $locale, atoum\tools\variable\analyzer $analyzer)
		{
			$this
				->given(
					$this->newTestedInstance
						->setLocale($locale)
						->setAnalyzer($analyzer),
					$this->calling($locale)->_ = $notAnException = uniqid(),
					$this->calling($analyzer)->getTypeOf = $type = uniqid()
				)
				->then
					->exception(function($test) use (& $line, & $value) { $line = __LINE__; $test->testedInstance->setWith($value = new \throwable); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($notAnException)
					->mock($locale)->call('_')->withArguments('%s is not an exception', $type)->once
					->mock($analyzer)->call('getTypeOf')->withArguments($value)->once
					->object($this->testedInstance->getValue())->isEqualTo($value)
			;
		}

		/** @php >= 7.0.0 */
		public function testSetWithPhpGte7()
		{
			$this
				->given($this->newTestedInstance)
				->then
					->object($this->testedInstance->setWith($value = new \error()))->isTestedInstance
					->exception($this->testedInstance->getValue())->isIdenticalTo($value)
			;
		}

		public function testIsInstanceOf()
		{
			$this
				->given($this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
				)
				->then
					->exception(function($test) { $test->testedInstance->hasSize(rand(0, PHP_INT_MAX)); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Exception is undefined')

				->if($this->testedInstance->setWith(new \exception()))
				->then
					->object($this->testedInstance->isInstanceOf('\Exception'))->isTestedInstance
					->object($this->testedInstance->isInstanceOf('Exception'))->isTestedInstance
					->object($this->testedInstance->isInstanceOf('\exception'))->isTestedInstance
					->object($this->testedInstance->isInstanceOf('exception'))->isTestedInstance

					->exception(function($test) { $test->testedInstance->isInstanceOf(uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Argument of mageekguy\atoum\asserters\exception::isInstanceOf() must be a \exception instance or an exception class name')

				->if($this->calling($locale)->_ = $isNotAnInstance = uniqid())
				->then
					->exception(function($test) { $test->testedInstance->isInstanceOf('mageekguy\atoum\exceptions\runtime'); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($isNotAnInstance)
					->mock($locale)->call('_')->withArguments('%s is not an instance of %s', $this->testedInstance)->once

					->exception(function($test) use (& $failMessage) { $test->testedInstance->isInstanceOf('mageekguy\atoum\exceptions\runtime', $failMessage = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($failMessage)
			;
		}

		public function testHasCode()
		{
			$this
				->given($this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
				)
				->then
					->exception(function($test) { $test->testedInstance->hasCode(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
						->isInstanceOf('logicException')
						->hasMessage('Exception is undefined')

				->if($this->testedInstance->setWith(new \exception(uniqid(), $code = rand(2, PHP_INT_MAX))))
				->then
					->object($this->testedInstance->hasCode($code))->isTestedInstance

				->if($this->calling($locale)->_ = $hasNotCode = uniqid())
				->then
					->exception(function($test) use (& $badCode) { $test->testedInstance->hasCode($badCode = 1); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($hasNotCode)
					->mock($locale)->call('_')->withArguments('code is %s instead of %s', $code, $badCode)->once

					->exception(function($test) use (& $failMessage) { $test->testedInstance->hasCode(rand(1, PHP_INT_MAX), $failMessage = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($failMessage)
			;
		}

		public function testHasDefaultCode()
		{
			$this
				->given($this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
				)
				->then
					->exception(function($test) { $test->testedInstance->hasDefaultCode(); })
						->isInstanceOf('logicException')
						->hasMessage('Exception is undefined')

					->exception(function($test) { $test->testedInstance->hasDefaultCode; })
						->isInstanceOf('logicException')
						->hasMessage('Exception is undefined')

				->if($this->testedInstance->setWith(new \exception(uniqid())))
				->then
					->object($this->testedInstance->hasDefaultCode())->isTestedInstance
					->object($this->testedInstance->hasDefaultCode())->isTestedInstance

				->if(
					$this->testedInstance->setWith(new \exception(uniqid(), $code = rand(1, PHP_INT_MAX))),
					$this->calling($locale)->_ = $hasNotDefaultCode = uniqid()
				)
				->then
					->exception(function($test) { $test->testedInstance->hasDefaultCode(); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($hasNotDefaultCode)
					->mock($locale)->call('_')->withArguments('code is %s instead of 0', $code)->once

					->exception(function($test) { $test->testedInstance->hasDefaultCode; })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($hasNotDefaultCode)
					->mock($locale)->call('_')->withArguments('code is %s instead of 0', $code)->twice

					->exception(function($test) use (& $failMessage) { $test->testedInstance->hasDefaultCode($failMessage = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($failMessage)
			;
		}

		public function testHasMessage()
		{
			$this
				->given($this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
				)
				->then
					->exception(function($test) { $test->testedInstance->hasMessage(uniqid()); })
						->isInstanceOf('logicException')
						->hasMessage('Exception is undefined')

				->if($this->testedInstance->setWith(new \exception($message = uniqid())))
				->then
					->object($this->testedInstance->hasMessage($message))->isTestedInstance

				->if($this->calling($locale)->_ = $hasNotMessage = uniqid())
				->then
					->exception(function($test) use (& $badMessage) { $test->testedInstance->hasMessage($badMessage = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($hasNotMessage)
					->mock($locale)->call('_')->withArguments('message \'%s\' is not identical to \'%s\'', $message, $badMessage)->once

					->exception(function($test) use (& $failMessage) { $test->testedInstance->hasMessage(uniqid(), $failMessage = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($failMessage)
			;
		}

		public function testHasNestedException()
		{
			$this
				->if($this->newTestedInstance)
				->then
					->exception(function($test) { $test->testedInstance->hasNestedException(); })
						->isInstanceOf('logicException')
						->hasMessage('Exception is undefined')

				->if(
					$this->testedInstance
						->setWith(new \exception())
						->setLocale($locale = new \mock\atoum\locale()),
					$this->calling($locale)->_ = $hasNoNestedException = uniqid()
				)
				->then
					->exception(function($test) { $test->testedInstance->hasNestedException(); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($hasNoNestedException)
					->mock($locale)->call('_')->withArguments('exception does not contain any nested exception')->once

					->exception(function($test) { $test->testedInstance->hasNestedException; })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($hasNoNestedException)
					->mock($locale)->call('_')->withArguments('exception does not contain any nested exception')->twice

					->exception(function($test) use (& $failMessage) { $test->testedInstance->hasNestedException(null, $failMessage = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($failMessage)

					->exception(function($test) { $test->testedInstance->hasNestedException(new \exception()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($hasNoNestedException)
					->mock($locale)->call('_')->withArguments('exception does not contain this nested exception')->once

				->if($this->testedInstance->setWith(new \exception(uniqid(), rand(1, PHP_INT_MAX), $nestedException = new \exception())))
				->then
					->object($this->testedInstance->hasNestedException())->isTestedInstance

					->object($this->testedInstance->hasNestedException($nestedException))->isTestedInstance

					->exception(function($test) { $test->testedInstance->hasNestedException(new \exception()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($hasNoNestedException)
					->mock($locale)->call('_')->withArguments('exception does not contain this nested exception')->twice
			;
		}

		public function testMessage()
		{
			$this
				->if($this->newTestedInstance)
				->then
					->exception(function($test) { $test->testedInstance->message; })
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Exception is undefined')

					->exception(function($test) { $test->testedInstance->mESSAGe; })
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Exception is undefined')

				->if($this->testedInstance->setWith(new \exception('')))
				->then
					->object($string = $this->testedInstance->message)->isInstanceOf('mageekguy\atoum\asserters\phpString')
					->string($string->getValue())->isEqualTo('')

					->object($string = $this->testedInstance->MesSAge)->isInstanceOf('mageekguy\atoum\asserters\phpString')
					->string($string->getValue())->isEqualTo('')

				->if($this->testedInstance->setWith(new \exception($message = uniqid())))
				->then
					->object($string = $this->testedInstance->message)->isInstanceOf('mageekguy\atoum\asserters\phpString')
					->string($string->getValue())->isEqualTo($message)

					->object($string = $this->testedInstance->meSSAGe)->isInstanceOf('mageekguy\atoum\asserters\phpString')
					->string($string->getValue())->isEqualTo($message)
			;
		}

		public function testGetLastValue()
		{
			$this
				->variable(asserters\exception::getLastValue())->isNull()

				->if(
					$this->newTestedInstance->setWith(function() use (& $exception) { $exception = new \exception(); throw $exception; })
				)
				->then
					->object(asserters\exception::getLastValue())->isIdenticalTo($exception)

				->if($this->testedInstance->setWith(function() use (& $otherException) { $otherException = new \exception(); throw $otherException; }))
				->then
					->object(asserters\exception::getLastValue())->isIdenticalTo($otherException)
			;
		}
	}
}
