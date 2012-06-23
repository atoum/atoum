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
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\php\tokenizer\iterator\value')
		;
	}

	public function test__construct()
	{
		$token = new tokenizer\token($tag = uniqid(), $string = uniqid(), $line = rand(1, PHP_INT_MAX));

		$this->assert
			->string($token->getTag())->isEqualTo($tag)
			->string($token->getValue())->isEqualTo($string)
			->integer($token->getLine())->isEqualTo($line)
			->variable($token->getParent())->isNull()
		;

		$token = new tokenizer\token($tag = uniqid(), $string = uniqid(), $line = rand(1, PHP_INT_MAX), $parent = new tokenizer\iterator());

		$this->assert
			->string($token->getTag())->isEqualTo($tag)
			->string($token->getValue())->isEqualTo($string)
			->integer($token->getLine())->isEqualTo($line)
			->object($token->getParent())->isIdenticalTo($parent)
		;
	}

	public function test__toString()
	{
		$token = new tokenizer\token($tag = uniqid(), $string = uniqid(), $line = rand(1, PHP_INT_MAX));

		$this->assert
			->castToString($token)->isEqualTo($string)
		;

		$token = new tokenizer\token($tag = uniqid(), null, $line = rand(1, PHP_INT_MAX));

		$this->assert
			->castToString($token)->isEqualTo($tag)
		;
	}

	public function testCount()
	{
		$token = new tokenizer\token(uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->sizeOf($token)->isEqualTo(1)
		;
	}

	public function testKey()
	{
		$token = new tokenizer\token(uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->integer($token->key())->isZero(0)
		;

		$token->next();

		$this->assert
			->variable($token->key())->isNull()
		;

		$token->rewind();

		$this->assert
			->integer($token->key())->isZero(0)
		;
	}

	public function testCurrent()
	{
		$token = new tokenizer\token(uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->object($token->current())->isIdenticalTo($token)
		;

		$token->next();

		$this->assert
			->variable($token->current())->isNull()
		;

		$token->rewind();

		$this->assert
			->object($token->current())->isIdenticalTo($token)
		;
	}

	public function testPrev()
	{
		$token = new tokenizer\token(uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->integer($token->key())->isZero()
			->object($token->current())->isIdenticalTo($token)
			->object($token->prev())->isIdenticalTo($token)
			->variable($token->key())->isNull()
			->variable($token->current())->isNull()
		;
	}

	public function testNext()
	{
		$token = new tokenizer\token(uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->integer($token->key())->isZero()
			->object($token->current())->isIdenticalTo($token)
			->object($token->next())->isIdenticalTo($token)
			->variable($token->key())->isNull()
			->variable($token->current())->isNull()
		;
	}

	public function testSetParent()
	{
		$token = new tokenizer\token(uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->variable($token->getParent())->isNull()
			->object($token->setParent($parent = new tokenizer\iterator()))->isIdenticalTo($token)
			->object($token->getParent())->isIdenticalTo($parent)
			->sizeOf($parent)->isEqualTo(1)
			->object($parent->current())->isIdenticalTo($token)
			->exception(function() use ($token) {
						$token->setParent(new tokenizer\iterator());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Parent is already set')
			;
		;
	}

	public function testAppend()
	{
		$token = new tokenizer\token(uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
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
		$token = new tokenizer\token(uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->integer($token->key())->isZero()
			->object($token->current())->isIdenticalTo($token)
			->object($token->end())->isIdenticalTo($token)
			->integer($token->key())->isZero()
			->object($token->current())->isIdenticalTo($token)
		;
	}

	public function testSeek()
	{
		$token = new tokenizer\token(uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->integer($token->key())->isZero()
			->object($token->current())->isIdenticalTo($token)
			->object($token->seek(rand(1, PHP_INT_MAX)))->isIdenticalTo($token)
			->variable($token->key())->isNull()
			->variable($token->current())->isNull()
			->object($token->seek(0))->isIdenticalTo($token)
			->integer($token->key())->isZero()
			->object($token->current())->isIdenticalTo($token)
		;
	}
}
