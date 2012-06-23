<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\tools\diffs
;

require_once __DIR__ . '/../../runner.php';

class string extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\variable');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\string($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->getAdapter())->isEqualTo(new atoum\adapter())
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function test__toString()
	{
		$this
			->if($asserter = new asserters\string($generator = new asserter\generator()))
			->and($asserter->setWith($value = uniqid()))
			->then
				->castToString($asserter)->isEqualTo('string(' . strlen($value) . ') \'' . $value . '\'')
			->if($asserter->setWith($value = "\010" . uniqid() . "\010", null, $charlist = "\010"))
			->then
				->castToString($asserter)->isEqualTo('string(' . strlen($value) . ') \'' . addcslashes($value, "\010") . '\'')
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\string($generator = new asserter\generator()))
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
			->if($asserter = new asserters\string($generator = new asserter\generator()))
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
					->hasMessage($generator->getLocale()->_('strings are not equals') . PHP_EOL . $diff->setReference($secondString)->setData($firstString))
			->object($asserter->isEqualTo($firstString))->isIdenticalTo($asserter)
		;
	}

	public function testIsEqualToFileContents()
	{
		$this
			->if($asserter = new asserters\string($generator = new asserter\generator(), $adapter = new atoum\test\adapter()))
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
					->hasMessage(sprintf($generator->getLocale()->_('string is not equals to contents of file %s'), $path) . PHP_EOL . $diff->setReference($fileContents)->setData($firstString))
			->if($adapter->file_get_contents = $firstString)
			->then
				->object($asserter->isEqualToContentsOfFile(uniqid()))->isIdenticalTo($asserter)
		;
	}

	public function testIsEmpty()
	{
		$this
			->if($asserter = new asserters\string($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($string = uniqid()))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($generator->getLocale()->_('strings are not equals') . PHP_EOL . $diff->setReference('')->setData($string))
			->if($asserter->setWith(''))
			->then
				->object($asserter->isEmpty())->isIdenticalTo($asserter)
		;
	}

	public function testIsNotEmpty()
	{
		$this
			->if($asserter = new asserters\string($generator = new asserter\generator()))
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
			->if($asserter = new asserters\string($generator = new asserter\generator()))
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

	public function testContains()
	{
		$this
			->if($asserter = new asserters\string($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($string = uniqid()))
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
}
