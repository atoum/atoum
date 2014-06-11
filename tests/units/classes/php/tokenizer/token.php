<?php

namespace mageekguy\atoum\tests\units\php\tokenizer;

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer
;

require_once __DIR__ . '/../../../runner.php';

class token extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass->isSubclassOf('mageekguy\atoum\php\tokenizer\iterator\value')
		;
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance($tag = uniqid(), $string = uniqid(), $line = rand(1, PHP_INT_MAX)))
			->then
				->string($this->testedInstance->getTag())->isEqualTo($tag)
				->string($this->testedInstance->getValue())->isEqualTo($string)
				->integer($this->testedInstance->getLine())->isEqualTo($line)
				->variable($this->testedInstance->getParent())->isNull()
			->if($this->newTestedInstance($tag = uniqid(), $string = uniqid(), $line = rand(1, PHP_INT_MAX), $parent = new tokenizer\iterator()))
			->then
				->string($this->testedInstance->getTag())->isEqualTo($tag)
				->string($this->testedInstance->getValue())->isEqualTo($string)
				->integer($this->testedInstance->getLine())->isEqualTo($line)
				->object($this->testedInstance->getParent())->isIdenticalTo($parent)
		;
	}

	public function test__toString()
	{
		$this
			->if($this->newTestedInstance($tag = uniqid(), $string = uniqid(), $line = rand(1, PHP_INT_MAX)))
			->then
				->castToString($this->testedInstance)->isEqualTo($string)
			->if($this->newTestedInstance($tag = uniqid(), null, $line = rand(1, PHP_INT_MAX)))
			->then
				->castToString($this->testedInstance)->isEqualTo($tag)
		;
	}

	public function testCount()
	{
		$this
			->if($this->newTestedInstance(uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->sizeOf($this->testedInstance)->isEqualTo(1)
		;
	}

	public function testKey()
	{
		$this
			->if($this->newTestedInstance(uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->integer($this->testedInstance->key())->isZero(0)
			->if($this->testedInstance->next())
			->then
				->variable($this->testedInstance->key())->isNull()
			->if($this->testedInstance->rewind())
			->then
				->integer($this->testedInstance->key())->isZero(0)
		;
	}

	public function testCurrent()
	{
		$this
			->if($this->newTestedInstance(uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->object($this->testedInstance->current())->isTestedInstance
			->if($this->testedInstance->next())
			->then
				->variable($this->testedInstance->current())->isNull()
			->if($this->testedInstance->rewind())
			->then
				->object($this->testedInstance->current())->isTestedInstance
		;
	}

	public function testPrev()
	{
		$this
			->if($this->newTestedInstance(uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isTestedInstance
				->object($this->testedInstance->prev())->isTestedInstance
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
		;
	}

	public function testNext()
	{
		$this
			->if($this->newTestedInstance(uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isTestedInstance
				->object($this->testedInstance->next())->isTestedInstance
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
		;
	}

	public function testSetParent()
	{
		$this
			->if($token = $this->newTestedInstance(uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->variable($this->testedInstance->getParent())->isNull()
				->object($this->testedInstance->setParent($parent = new tokenizer\iterator()))->isTestedInstance
				->object($this->testedInstance->getParent())->isIdenticalTo($parent)
				->sizeOf($parent)->isEqualTo(1)
				->object($parent->current())->isTestedInstance
				->exception(function() use ($token) {
							$token->setParent(new tokenizer\iterator());
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Parent is already set')
		;
	}

	public function testAppend()
	{
		$this
			->if($token = $this->newTestedInstance(uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->exception(function() use ($token) {
							$token->append(new tokenizer\token(uniqid(), uniqid(), rand(1, PHP_INT_MAX)));
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage($this->getTestedClassName() . '::append() is unavailable')
		;
	}

	public function testEnd()
	{
		$this
			->if($this->newTestedInstance(uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isTestedInstance
				->object($this->testedInstance->end())->isTestedInstance
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isTestedInstance
		;
	}

	public function testSeek()
	{
		$this
			->if($this->newTestedInstance(uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isTestedInstance
				->object($this->testedInstance->seek(rand(1, PHP_INT_MAX)))->isTestedInstance
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
				->object($this->testedInstance->seek(0))->isTestedInstance
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isTestedInstance
		;
	}
}
