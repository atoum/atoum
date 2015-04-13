<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\tools\variable
;

require_once __DIR__ . '/../../runner.php';

/** @extensions mbstring */
class utf8String extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\phpString');
	}

	public function test__construct()
	{
		$this

			->if($this->function->extension_loaded = false)
			->then
				->exception(function() { new asserters\utf8String(); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('mbstring PHP extension is mandatory to use utf8String asserter')
				->function('extension_loaded')->wasCalledWithArguments('mbstring')->once

			->if($this->function->extension_loaded = true)
			->given($asserter = $this->newTestedInstance)
			->then
				->object($asserter->getGenerator())->isEqualTo(new asserter\generator())
				->object($asserter->getAnalyzer())->isEqualTo(new variable\analyzer())
				->object($asserter->getLocale())->isEqualTo(new atoum\locale())
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()

			->if($asserter = $this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
			->then
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->getAnalyzer())->isIdenticalTo($analyzer)
				->object($asserter->getLocale())->isIdenticalTo($locale)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function test__toString()
	{
		$this
			->given($asserter = $this->newTestedInstance)

			->if($asserter->setWith($value = $this->getRandomUtf8String()))
			->then
				->castToString($asserter)->isEqualTo('string(' . mb_strlen($value, 'UTF-8') . ') \'' . $value . '\'')

			->if($asserter->setWith($value = "\010" . $this->getRandomUtf8String() . "\010", $charlist = "\010"))
			->then
				->castToString($asserter)->isEqualTo('string(' . mb_strlen($value, 'UTF-8') . ') \'' . addcslashes($value, "\010") . '\'')
		;
	}

	public function testSetWith()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
					->setLocale($locale = new \mock\atoum\locale())
			)

			->if(
				$this->calling($analyzer)->getTypeOf = $type = uniqid(),
				$this->calling($locale)->_ = $notString = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith(null); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notString)
				->mock($locale)->call('_')->withArguments('%s is not a string', $type)->once
				->mock($analyzer)
					->call('isString')->withArguments(null)->once
					->call('isUtf8')->withArguments(null)->never

			->if(
				$this->calling($analyzer)->isString = true,
				$this->calling($analyzer)->isUtf8 = false,
				$this->calling($locale)->_ = $notUtf8String = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith(null); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notUtf8String)
				->mock($locale)->call('_')->withArguments('%s is not an UTF-8 string', $type)->once
				->mock($analyzer)
					->call('isString')->withArguments(null)->twice
					->call('isUtf8')->withArguments(null)->once

			->if($this->calling($analyzer)->isUtf8 = true)
			->then
				->object($asserter->setWith(null))->isIdenticalTo($asserter)
		;
	}

	public function testHasLength()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->hasLength(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if(
				$asserter
					->setWith('')
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $hasNotLength = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $requiredLength) { $asserter->hasLength($requiredLength = rand(1, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($hasNotLength)
				->mock($locale)->call('_')->withArguments('length of %s is not %d', $asserter, $requiredLength)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->hasLength(rand(1, PHP_INT_MAX), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->then
				->object($asserter->hasLength(mb_strlen($string, 'UTF-8')))->isIdenticalTo($asserter)
		;
	}

	public function testHasLengthGreaterThan()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->hasLengthGreaterThan(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if(
				$asserter
					->setWith('Chuck Norris')
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $lengthNotGreater = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $requiredLength) { $asserter->hasLengthGreaterThan($requiredLength = rand(1, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($lengthNotGreater)
				->mock($locale)->call('_')->withArguments('length of %s is not greater than %d', $asserter, $requiredLength)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->hasLengthGreaterThan(rand(1, PHP_INT_MAX), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->then
				->object($asserter->hasLengthGreaterThan(mb_strlen($string, 'UTF-8') - 1))->isIdenticalTo($asserter)
		;
	}

	public function testHasLengthLessThan()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->hasLengthLessThan(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if(
				$asserter
					->setWith('Chuck Norris')
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $lengthNotLess = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $requiredLength) { $asserter->hasLengthLessThan($requiredLength = 10); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($lengthNotLess)
				->mock($locale)->call('_')->withArguments('length of %s is not less than %d', $asserter, $requiredLength)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->hasLengthLessThan(10, $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->then
				->object($asserter->hasLengthLessThan(strlen($string) + 1))->isIdenticalTo($asserter)
		;
	}

	public function testContains()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if(
				$asserter
					->setWith($string = $this->getRandomUtf8String())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $notContains = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $fragment) { $asserter->contains($fragment = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notContains)
				->mock($locale)->call('_')->withArguments('%s does not contain %s', $asserter, $fragment)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->contains(uniqid(), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($asserter->setWith(uniqid() . $string . uniqid()))
			->then
				->object($asserter->contains(mb_substr($string, 2, 6, 'UTF-8')))->isIdenticalTo($asserter)

				->exception(function() use ($asserter, $string, & $fragment) { $asserter->contains($fragment = mb_strtoupper($string, 'UTF-8')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notContains)
				->mock($locale)->call('_')->withArguments('%s does not contain %s', $asserter, $fragment)->once
		;
	}

	public function testNotContains()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->notContains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if(
				$asserter
					->setWith($string = $this->getRandomUtf8String())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $contains = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $fragment) { $asserter->notContains($fragment = mb_substr($asserter->getValue(), 2, 6, 'UTF-8')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($contains)
				->mock($locale)->call('_')->withArguments('%s contains %s', $asserter, $fragment)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->notContains(mb_substr($asserter->getValue(), 2, 6, 'UTF-8'), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->object($asserter->notContains('agent'))->isIdenticalTo($asserter)
				->object($asserter->notContains(uniqid()))->isIdenticalTo($asserter)
		;
	}

	public function testStartWith()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->startWith(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if(
				$asserter
					->setWith($this->getRandomUtf8String())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $notStartWith = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $fragment) { $asserter->startWith($fragment = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notStartWith)
				->mock($locale)->call('_')->withArguments('%s does not start with %s', $asserter, $fragment)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->startWith(uniqid(), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter, & $fragment) { $asserter->startWith($fragment = mb_strtoupper(substr($asserter->getValue(), 0, 6), 'UTF-8')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notStartWith)
				->mock($locale)->call('_')->withArguments('%s does not start with %s', $asserter, $fragment)->once

				->exception(function() use ($asserter, & $fragment) { $asserter->startWith($fragment = substr($asserter->getValue(), 0, 6) . uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notStartWith)
				->mock($locale)->call('_')->withArguments('%s does not start with %s', $asserter, $fragment)->once

				->object($asserter->startWith(substr($asserter->getValue(), 0, 6)))->isIdenticalTo($asserter)
		;
	}

	public function testNotStartWith()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->notStartWith(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if(
				$asserter
					->setWith($this->getRandomUtf8String())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $startWith = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $fragment) { $asserter->notStartWith($fragment = substr($asserter->getValue(), 0, 6)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($startWith)
				->mock($locale)->call('_')->withArguments('%s start with %s', $asserter, $fragment)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->notStartWith(substr($asserter->getValue(), 0, 6), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->object($asserter->notStartWith(mb_strtoupper(substr($asserter->getValue(), 0, 6), 'UTF-8')))->isIdenticalTo($asserter)
				->object($asserter->notStartWith(uniqid()))->isIdenticalTo($asserter)
		;
	}

	public function testEndWith()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->endWith(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if(
				$asserter
					->setWith($string = $this->getRandomUtf8String())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $notEndWith = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $fragment) { $asserter->endWith($fragment = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notEndWith)
				->mock($locale)->call('_')->withArguments('%s does not end with %s', $asserter, $fragment)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->endWith(uniqid(), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter, & $failMessage, & $fragment) { $asserter->endWith($fragment = mb_strtoupper(mb_substr($asserter->getValue(), -6, mb_strlen($asserter->getValue(), 'UTF-8'), 'UTF-8'), 'UTF-8')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notEndWith)
				->mock($locale)->call('_')->withArguments('%s does not end with %s', $asserter, $fragment)->once

				->exception(function() use ($asserter, & $fragment) { $asserter->endWith($fragment = uniqid() . mb_substr($asserter->getValue(), -6, mb_strlen($asserter->getValue(), 'UTF-8'), 'UTF-8')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notEndWith)
				->mock($locale)->call('_')->withArguments('%s does not end with %s', $asserter, $fragment)->once

				->object($asserter->endWith(mb_substr($string, -6, mb_strlen($asserter->getValue(), 'UTF-8'), 'UTF-8')))->isIdenticalTo($asserter)
		;
	}

	public function testNotEndWith()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->notEndWith(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if(
				$asserter
					->setWith($this->getRandomUtf8String())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $endWith = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $fragment) { $asserter->notEndWith($fragment = mb_substr($asserter->getValue(), -6, mb_strlen($asserter->getValue(), 'UTF-8'), 'UTF-8')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($endWith)
				->mock($locale)->call('_')->withArguments('%s end with %s', $asserter, $fragment)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->notEndWith(mb_substr($asserter->getValue(), -6, mb_strlen($asserter->getValue(), 'UTF-8'), 'UTF-8'), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->object($asserter->notEndWith(mb_strtoupper(mb_substr($asserter->getValue(), -6, mb_strlen($asserter->getValue(), 'UTF-8'), 'UTF-8'), 'UTF-8')))->isIdenticalTo($asserter)
				->object($asserter->notEndWith(uniqid()))->isIdenticalTo($asserter)
		;
	}

	public function testLength()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->length; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(''))
			->then
				->object($integer = $asserter->length)
					->isInstanceOf('mageekguy\atoum\asserters\integer')
				->integer($integer->getValue())
					->isEqualTo(0)

			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->then
				->object($integer = $asserter->length)
					->isInstanceOf('mageekguy\atoum\asserters\integer')
				->integer($integer->getValue())
					->isEqualTo(mb_strlen($string, 'UTF-8'))
		;
	}

	private function getRandomUtf8String()
	{
		$characters = 'àâäéèêëîïôöùüŷÿ';
		$characters = mb_convert_encoding($characters, 'UTF-8', mb_detect_encoding($characters));
		$charactersLength = mb_strlen($characters, 'UTF-8');

		$utf8String = '';

		for($i = 0; $i < 16; $i++)
		{
			$utf8String .= mb_substr($characters, rand(0, $charactersLength - 1), 1, 'UTF-8');
		}

		return $utf8String;
	}
}
