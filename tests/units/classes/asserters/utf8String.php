<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\asserters\utf8String as sut
;

require_once __DIR__ . '/../../runner.php';

/** @extensions mbstring */
class utf8String extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\string');
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
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getAdapter())->isEqualTo(new atoum\adapter())
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
			->if($adapter = new atoum\test\adapter())
			->and($adapter->extension_loaded = true)
			->and($asserter = new sut($generator = new asserter\generator(), $adapter))
			->then
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getAdapter())->isEqualTo($adapter)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
			->if($adapter->extension_loaded = false)
			->then
				->exception(function() use ($adapter) { new sut(new asserter\generator(), $adapter); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('mbstring PHP extension is mandatory to use utf8String asserter')
		;
	}

	public function test__toString()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($value = $this->getRandomUtf8String()))
			->then
				->castToString($asserter)->isEqualTo('string(' . mb_strlen($value, 'UTF-8') . ') \'' . $value . '\'')
			->if($asserter->setWith($value = "\010" . $this->getRandomUtf8String() . "\010", null, $charlist = "\010"))
			->then
				->castToString($asserter)->isEqualTo('string(' . mb_strlen($value, 'UTF-8') . ') \'' . addcslashes($value, "\010") . '\'')
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setwith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isinstanceof('mageekguy\atoum\asserter\exception')
					->hasmessage(sprintf($generator->getlocale()->_('%s is not a string'), $asserter->gettypeof($value)))
				->exception(function() use ($asserter, & $value) { $asserter->setwith("\xf0\x28\x8c\xbc"); })
					->isinstanceof('mageekguy\atoum\asserter\exception')
					->hasmessage(sprintf($generator->getlocale()->_('\'%s\' is not an UTF-8 string'), "\xf0\x28\x8c\xbc"))
				->exception(function() use ($asserter, & $value) { $asserter->setwith("\xf8\xa1\xa1\xa1\xa1"); })
					->isinstanceof('mageekguy\atoum\asserter\exception')
					->hasmessage(sprintf($generator->getlocale()->_('\'%s\' is not an UTF-8 string'), "\xf8\xa1\xa1\xa1\xa1"))
				->object($asserter->setWith(uniqid()))->isIdenticalTo($asserter)
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
			->if($asserter->setWith($firstString = $this->getRandomUtf8String()))
			->and($diff = new diffs\variable())
			->and($secondString = $this->getRandomUtf8String())
			->then
				->exception(function() use ($asserter, $secondString) { $asserter->isEqualTo($secondString); })
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
			->if($asserter->setWith($firstString = $this->getRandomUtf8String()))
			->and($adapter->file_get_contents = false)
			->then
				->exception(function() use ($asserter, & $path) { $asserter->isEqualToContentsOfFile($path = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Unable to get contents of file %s'), $path))
			->if($adapter->file_get_contents = $fileContents = $this->getRandomUtf8String())
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $path) { $asserter->isEqualToContentsOfFile($path); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('string is not equals to contents of file %s'), $path) . PHP_EOL . $diff->setExpected($fileContents)->setActual($firstString))
			->if($adapter->file_get_contents = $firstString)
			->then
				->object($asserter->isEqualToContentsOfFile($this->getRandomUtf8String()))->isIdenticalTo($asserter)
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
			->if($asserter->setWith($string = $this->getRandomUtf8String()))
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
			->if($asserter->setWith($string = $this->getRandomUtf8String()))
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
			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->then
				->object($asserter->hasLength(mb_strlen($string, 'UTF-8')))->isIdenticalTo($asserter)
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
			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, $string) { $asserter->hasLengthGreaterThan(mb_strlen($string, 'UTF-8')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('length of %s is not greater than %d'), $asserter->getTypeOf($string), mb_strlen($string, 'UTF-8')))
				->object($asserter->hasLengthGreaterThan(0))->isIdenticalTo($asserter)
			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->then
				->object($asserter->hasLengthGreaterThan(mb_strlen($string, 'UTF-8') - 1))->isIdenticalTo($asserter)
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
			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, $string) { $asserter->hasLengthLessThan(mb_strlen($string, 'UTF-8')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('length of %s is not less than %d'), $asserter->getTypeOf($string), mb_strlen($string, 'UTF-8')))
				->object($asserter->hasLengthLessThan(20))->isIdenticalTo($asserter)
			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->then
				->object($asserter->hasLengthLessThan(mb_strlen($string, 'UTF-8') + 1))->isIdenticalTo($asserter)
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
			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->and($diff = new diffs\variable())
			->and($fragment = $this->getRandomUtf8String())
			->then
				->exception(function() use ($asserter, $fragment) { $asserter->contains($fragment); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String does not contain %s'), $fragment))
				->object($asserter->contains($string))->isIdenticalTo($asserter)
			->if($asserter->setWith($this->getRandomUtf8String() . $string . $this->getRandomUtf8String()))
			->then
				->object($asserter->contains($string))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, $string, & $fragment) { $asserter->contains($fragment = mb_strtoupper($string, 'UTF-8')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String does not contain %s'), $fragment))
				->exception(function() use ($asserter) {
							$asserter->contains("\xf0\x28\x8c\xbc");
						}
					)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Fragment \'' . "\xf0\x28\x8c\xbc" . '\' is not an UTF-8 string')
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
			->and($fragment = 'Agent')
			->then
				->exception(function() use ($asserter, $fragment) { $asserter->notContains($fragment); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String contains %s'), $fragment))
				->object($asserter->notContains('coach'))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) {
							$asserter->notContains("\xf0\x28\x8c\xbc");
						}
					)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Fragment \'' . "\xf0\x28\x8c\xbc" . '\' is not an UTF-8 string')
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
			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->and($diff = new diffs\variable())
			->and($fragment = $this->getRandomUtf8String())
			->then
				->exception(function() use ($asserter, $fragment) { $asserter->startWith($fragment); })
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
				->exception(function() use ($asserter, $string, & $fragment) { $asserter->startWith($fragment = mb_strtoupper($string, 'UTF-8')); })
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
			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->then
				->exception(function() use ($asserter, $string) { $asserter->notStartWith($string); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String start with %s'), $string))
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
			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->and($diff = new diffs\variable())
			->and($fragment = $this->getRandomUtf8String())
			->then
				->exception(function() use ($asserter, $fragment) { $asserter->endWith($fragment); })
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
				->exception(function() use ($asserter, $string, & $fragment) { $asserter->endWith($fragment = mb_strtoupper($string, 'UTF-8')); })
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
			->if($asserter->setWith($string = $this->getRandomUtf8String()))
			->then
				->exception(function() use ($asserter, $string) { $asserter->notEndWith($string); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('String end with %s'), $string))
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
			->if($asserter->setWith($str = $this->getRandomUtf8String()))
			->then
				->object($integer = $asserter->length)
					->isInstanceOf('mageekguy\atoum\asserters\integer')
				->integer($integer->getValue())
					->isEqualTo(mb_strlen($str, 'UTF-8'))
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
