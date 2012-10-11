<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\tools\diffs
;

require_once __DIR__ . '/../../runner.php';

class utf8String extends atoum\test
{
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

	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\string');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\utf8String($generator = new asserter\generator()))
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
			->if($asserter = new asserters\utf8String($generator = new asserter\generator()))
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
			->if($asserter = new asserters\utf8String($generator = new asserter\generator()))
			->then
				->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not a string'), $asserter->getTypeOf($value)))
				->integer($asserter->getValue())->isEqualTo($value)
				->variable($asserter->getCharlist())->isNull()
				->object($asserter->setWith($value = $this->getRandomUtf8String()))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo($value)
				->variable($asserter->getCharlist())->isNull()
				->object($asserter->setWith($value = $this->getRandomUtf8String(), null, $charlist = "\010"))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo($value)
				->string($asserter->getCharlist())->isEqualTo($charlist)
		;
	}

	public function testIsEqualTo()
	{
		$this
			->if($asserter = new asserters\utf8String($generator = new asserter\generator()))
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
					->hasMessage($generator->getLocale()->_('strings are not equals') . PHP_EOL . $diff->setReference($secondString)->setData($firstString))
			->object($asserter->isEqualTo($firstString))->isIdenticalTo($asserter)
		;
	}

	public function testIsEqualToFileContents()
	{
		$this
			->if($asserter = new asserters\utf8String($generator = new asserter\generator(), $adapter = new atoum\test\adapter()))
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
					->hasMessage(sprintf($generator->getLocale()->_('string is not equals to contents of file %s'), $path) . PHP_EOL . $diff->setReference($fileContents)->setData($firstString))
			->if($adapter->file_get_contents = $firstString)
			->then
				->object($asserter->isEqualToContentsOfFile($this->getRandomUtf8String()))->isIdenticalTo($asserter)
		;
	}

	public function testIsEmpty()
	{
		$this
			->if($asserter = new asserters\utf8String($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($string = $this->getRandomUtf8String()))
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
			->if($asserter = new asserters\utf8String($generator = new asserter\generator()))
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
			->if($asserter = new asserters\utf8String($generator = new asserter\generator()))
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

	public function testContains()
	{
		$this
			->if($asserter = new asserters\utf8String($generator = new asserter\generator()))
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
		;
	}
}
