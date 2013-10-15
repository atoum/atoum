<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\asserters\string as sut
;

require_once __DIR__ . '/../../runner.php';

class string extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\variable');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new sut())
			->then
				->object($asserter->getGenerator())->isEqualTo(new asserter\generator())
				->object($asserter->getLocale())->isIdenticalTo($asserter->getGenerator()->getLocale())
				->object($asserter->getAdapter())->isEqualTo(new atoum\adapter())
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
			->if($asserter = new sut($generator = new asserter\generator(), $adapter = new atoum\adapter()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function test__toString()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($value = uniqid()))
			->then
				->castToString($asserter)->isEqualTo('string(' . strlen($value) . ') \'' . $value . '\'')
			->if($asserter->setWith($value = "\010" . uniqid() . "\010", null, $charlist = "\010"))
			->then
				->castToString($asserter)->isEqualTo('string(' . strlen($value) . ') \'' . addcslashes($value, "\010") . '\'')
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($asserter = new sut())
			->then
				->object($asserter->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
				->object($asserter->setAdapter())->isIdenticalTo($asserter)
				->object($asserter->getAdapter())
					->isNotIdenticalTo($adapter)
					->isEqualTo(new atoum\adapter())
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not a string'), $asserter->getTypeOf($value)))
				->integer($asserter->getValue())->isEqualTo($value)
				->variable($asserter->getCharlist())->isNull()
				->object($asserter->setWith($value = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo($value)
				->variable($asserter->getCharlist())->isNull()
				->object($asserter->setWith($value = uniqid(), null, $charlist = "\010"))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo($value)
				->string($asserter->getCharlist())->isEqualTo($charlist)
		;
	}

	public function testIsEqualTo()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->isEqualTo(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($firstString = uniqid()))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $secondString) { $asserter->isEqualTo($secondString = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($generator->getLocale()->_('strings are not equals') . PHP_EOL . $diff->setExpected($secondString)->setActual($firstString))
			->object($asserter->isEqualTo($firstString))->isIdenticalTo($asserter)
		;
	}

	public function testIsEqualToFileContents()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator(), $adapter = new atoum\test\adapter()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->isEqualToContentsOfFile(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($firstString = uniqid()))
			->and($adapter->file_get_contents = false)
			->then
				->exception(function() use ($asserter, & $path) { $asserter->isEqualToContentsOfFile($path = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Unable to get contents of file %s'), $path))
			->if($adapter->file_get_contents = $fileContents = uniqid())
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $path) { $asserter->isEqualToContentsOfFile($path); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('string is not equals to contents of file %s'), $path) . PHP_EOL . $diff->setExpected($fileContents)->setActual($firstString))
			->if($adapter->file_get_contents = $firstString)
			->then
				->object($asserter->isEqualToContentsOfFile(uniqid()))->isIdenticalTo($asserter)
		;
	}

	public function testIsEmpty()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($string = uniqid()))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($generator->getLocale()->_('strings are not equals') . PHP_EOL . $diff->setExpected('')->setActual($string))
			->if($asserter->setWith(''))
			->then
				->object($asserter->isEmpty())->isIdenticalTo($asserter)
		;
	}

	public function testIsNotEmpty()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isNotEmpty(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith(''))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter) { $asserter->isNotEmpty(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($generator->getLocale()->_('string is empty'))
			->if($asserter->setWith($string = uniqid()))
			->then
				->object($asserter->isNotEmpty())->isIdenticalTo($asserter)
		;
	}

	public function testHasLength()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasLength(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith(''))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $requiredLength) { $asserter->hasLength($requiredLength = rand(1, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('length of %s is not %d'), $asserter->getTypeOf(''), $requiredLength))
				->object($asserter->hasLength(0))->isIdenticalTo($asserter)
			->if($asserter->setWith($string = uniqid()))
			->then
				->object($asserter->hasLength(strlen($string)))->isIdenticalTo($asserter)
		;
	}

	public function testHasLengthGreaterThan()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasLengthGreaterThan(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith('Chuck Norris'))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $requiredLength) { $asserter->hasLengthGreaterThan($requiredLength = rand(1, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('length of %s is not greater than %d'), $asserter->getTypeOf('Chuck Norris'), $requiredLength))
				->object($asserter->hasLengthGreaterThan(0))->isIdenticalTo($asserter)
			->if($asserter->setWith($string = uniqid()))
			->then
				->object($asserter->hasLengthGreaterThan(strlen($string)-1))->isIdenticalTo($asserter)
		;
	}

	public function testHasLengthLessThan()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasLengthLessThan(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith('Chuck Norris'))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $requiredLength) { $asserter->hasLengthLessThan($requiredLength = 10); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('length of %s is not less than %d'), $asserter->getTypeOf('Chuck Norris'), $requiredLength))
				->object($asserter->hasLengthLessThan(20))->isIdenticalTo($asserter)
			->if($asserter->setWith($string = uniqid()))
			->then
				->object($asserter->hasLengthLessThan(strlen($string)+1))->isIdenticalTo($asserter)
		;
	}

	public function testContains()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($string = __METHOD__))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $fragment) { $asserter->contains($fragment = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String does not contain %s'), $fragment))
				->object($asserter->contains($string))->isIdenticalTo($asserter)
			->if($asserter->setWith(uniqid() . $string . uniqid()))
			->then
				->object($asserter->contains($string))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, $string, & $fragment) { $asserter->contains($fragment = strtoupper($string)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String does not contain %s'), $fragment))
		;
	}

	public function testNotContains()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->notContains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($string = 'FreeAgent scans the field'))
			->then
				->exception(function() use ($asserter, & $fragment) { $asserter->notContains($fragment = 'Agent'); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String contains %s'), $fragment))
				->object($asserter->notContains('coach'))->isIdenticalTo($asserter)
		;
	}

	public function testStartWith()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->startWith(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($string = __METHOD__))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $fragment) { $asserter->startWith($fragment = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String does not start with %s'), $fragment))
				->object($asserter->startWith($string))->isIdenticalTo($asserter)
			->if($asserter->setWith(uniqid() . $string))
			->then
				->exception(function() use ($asserter, $string) { $asserter->startWith($string); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String does not start with %s'), $string))
			->if($asserter->setWith($string . uniqid()))
			->then
				->object($asserter->startWith($string))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, $string, & $fragment) { $asserter->startWith($fragment = strtoupper($string)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String does not start with %s'), $fragment))
		;
	}

	public function testNotStartWith()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->notStartWith(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($string = __METHOD__))
			->then
				->exception(function() use ($asserter, & $fragment) { $asserter->notStartWith($fragment = __NAMESPACE__); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String start with %s'), $fragment))
				->object($asserter->notStartWith(uniqid()))->isIdenticalTo($asserter)
		;
	}

	public function testEndWith()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->endWith(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($string = __METHOD__))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $fragment) { $asserter->endWith($fragment = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String does not end with %s'), $fragment))
				->object($asserter->endWith($string))->isIdenticalTo($asserter)
			->if($asserter->setWith($string . uniqid()))
			->then
				->exception(function() use ($asserter, $string) { $asserter->endWith($string); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String does not end with %s'), $string))
			->if($asserter->setWith(uniqid() . $string))
			->then
				->object($asserter->endWith($string))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, $string, & $fragment) { $asserter->endWith($fragment = strtoupper($string)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String does not end with %s'), $fragment))
		;
	}

	public function testNotEndWith()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->notEndWith(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($string = __METHOD__))
			->and($fragment = __FUNCTION__)
			->then
				->exception(function() use ($asserter, $fragment) { $asserter->notEndWith($fragment); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String end with %s'), $fragment))
				->object($asserter->notEndWith(uniqid()))->isIdenticalTo($asserter)
		;
	}

	public function testLength()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter->setWith($str = uniqid()))
			->then
				->object($integer = $asserter->length)
					->isInstanceOf('mageekguy\atoum\asserters\integer')
				->integer($integer->getValue())
					->isEqualTo(strlen($str))
		;
	}
}
