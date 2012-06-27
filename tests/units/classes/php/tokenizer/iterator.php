<?php

namespace mageekguy\atoum\tests\units\php\tokenizer;

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer
;

require_once __DIR__ . '/../../../runner.php';

class iterator extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->hasInterface('Iterator')
				->hasInterface('Countable')
		;
	}

	public function test__construct()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->array($iterator->getSkipedValues())->isEmpty()
			->sizeOf($iterator)->isZero()
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->current())->isNull()
			->variable($iterator->key())->isNull()
		;
	}

	public function test__toString()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->castToString($iterator)->isEmpty()
		;

		$iterator->append($token1 = new tokenizer\token(uniqid()));

		$this->assert
			->integer($iterator->key())->isZero()
			->castToString($iterator)->isEqualTo($token1)
			->integer($iterator->key())->isZero()
		;

		$iterator->append($token2 = new tokenizer\token(uniqid(), rand(1, PHP_INT_MAX)))->end();

		$this->assert
			->integer($iterator->key())->isEqualTo(1)
			->castToString($iterator)->isEqualTo($token1 . $token2)
			->integer($iterator->key())->isEqualTo(1)
		;

		$innerInnerIterator = new tokenizer\iterator();
		$innerInnerIterator->append($token3 = new tokenizer\token(uniqid()));

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append($token2 = new tokenizer\token(uniqid()))
			->append($innerInnerIterator)
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($innerIterator)
		;

		$this->assert
			->castToString($iterator)->isEqualTo($token1 . $token2 . $token3)
		;
	}

	public function testValid()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->boolean($iterator->valid())->isFalse()
		;

		$iterator->append(new tokenizer\token(uniqid()));

		$this->assert
			->boolean($iterator->valid())->isTrue()
		;

		$iterator->next();

		$this->assert
			->boolean($iterator->valid())->isFalse()
		;
	}

	public function testKey()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->variable($iterator->key())->isNull()
		;

		$iterator->append(new tokenizer\token(uniqid()));

		$this->assert
			->integer($iterator->key())->isZero()
		;

		$iterator->next();

		$this->assert
			->variable($iterator->key())->isNull()
		;

		$iterator->append(new tokenizer\token(uniqid()));

		$this->assert
			->integer($iterator->key())->isEqualTo(1)
		;

		$iterator->next();

		$this->assert
			->variable($iterator->key())->isNull()
		;

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append(new tokenizer\token(uniqid()))
			->append(new tokenizer\token(uniqid()))
			->append(new tokenizer\token(uniqid()))
		;

		$iterator
			->append($innerIterator)
			->append(new tokenizer\token(uniqid()))
		;

		$iterator->rewind();

		$this->assert
			->integer($iterator->key())->isEqualTo(0)
			->integer($iterator->next()->key())->isEqualTo(1)
			->integer($iterator->next()->key())->isEqualTo(2)
			->integer($iterator->next()->key())->isEqualTo(3)
			->integer($iterator->next()->key())->isEqualTo(4)
			->integer($iterator->next()->key())->isEqualTo(5)
			->variable($iterator->next()->key())->isNull()
		;
	}

	public function testCurrent()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->variable($iterator->current())->isNull()
		;

		$iterator->append($token1 = new tokenizer\token(uniqid()));

		$this->assert
			->object($iterator->current())->isIdenticalTo($token1)
			->variable($iterator->next()->current())->isNull()
		;

		$iterator->append($token2 = new tokenizer\token(uniqid()));

		$this->assert
			->object($iterator->current())->isIdenticalTo($token2)
			->variable($iterator->next()->current())->isNull()
		;

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append($token3 = new tokenizer\token(uniqid()))
			->append($token4 = new tokenizer\token(uniqid()))
			->append($token5 = new tokenizer\token(uniqid()))
		;

		$iterator
			->append($innerIterator)
			->append($token6 = new tokenizer\token(uniqid()))
		;

		$iterator->rewind();

		$this->assert
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->next()->current())->isIdenticalTo($token2)
			->object($iterator->next()->current())->isIdenticalTo($token3)
			->object($iterator->next()->current())->isIdenticalTo($token4)
			->object($iterator->next()->current())->isIdenticalTo($token5)
			->object($iterator->next()->current())->isIdenticalTo($token6)
			->variable($iterator->next()->current())->isNull()
		;
	}

	public function testPrev()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator->append($token1 = new tokenizer\token(uniqid()));

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator->append($token2 = new tokenizer\token(uniqid()))->end();

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($token2)
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$innerIterator = new tokenizer\iterator();

		$iterator->append($innerIterator)->end();

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($token2)
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator = new tokenizer\iterator();

		$iterator = new tokenizer\iterator();
		$iterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
			->append($token3 = new tokenizer\token(uniqid()))
			->end()
			->skipValue($token2)
		;

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->object($iterator->current())->isIdenticalTo($token3)
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(0)
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
			->append($token3 = new tokenizer\token(uniqid()))
			->end()
		;

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->object($iterator->current())->isIdenticalTo($token3)
			->object($iterator->prev(1))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($token2)
			->object($iterator->end()->prev(2))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->end()->prev(3))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
			->append($token3 = new tokenizer\token(uniqid()))
			->append($token4 = new tokenizer\token(uniqid()))
			->end()
			->skipValue($token2)
		;

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(3)
			->object($iterator->current())->isIdenticalTo($token4)
			->object($iterator->prev(1))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->object($iterator->current())->isIdenticalTo($token3)
			->object($iterator->end()->prev(2))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->end()->prev(rand(3, PHP_INT_MAX)))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;
	}

	public function testNext()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->object($iterator->next())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->object($iterator->next(rand(2, PHP_INT_MAX)))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator->append($token1 = new tokenizer\token(uniqid()));

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(0)
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->next())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator
			->append(new tokenizer\iterator())
			->append($token2 = new tokenizer\token(uniqid()))
			->rewind();
		;

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(0)
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->next())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($token2)
			->object($iterator->next())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
			->append($token3 = new tokenizer\token(uniqid()))
			->skipValue($token2)
		;

		$this->assert
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->next())->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(2)
			->object($iterator->current())->isIdenticalTo($token3)
			->object($iterator->next())->isIdenticalTo($iterator)
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
			->append($token3 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->next(1))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($token2)
			->object($iterator->rewind()->next(2))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->object($iterator->current())->isIdenticalTo($token3)
			->object($iterator->rewind()->next(rand(3, PHP_INT_MAX)))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
			->append($token3 = new tokenizer\token(uniqid()))
			->append($token4 = new tokenizer\token(uniqid()))
			->skipValue($token2)
		;

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->next(1))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->object($iterator->current())->isIdenticalTo($token3)
			->object($iterator->rewind()->next(2))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(3)
			->object($iterator->current())->isIdenticalTo($token4)
			->object($iterator->rewind()->next(rand(3, PHP_INT_MAX)))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;
	}

	public function testCount()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->sizeOf($iterator)->isEqualTo(0)
		;

		$iterator->append(new tokenizer\token(uniqid()));

		$this->assert
			->sizeOf($iterator)->isEqualTo(1)
		;

		$innerIterator = new tokenizer\iterator();

		$iterator->append($innerIterator);

		$this->assert
			->sizeOf($iterator)->isEqualTo(1)
		;

		$innerIterator->append(new tokenizer\token(uniqid()));

		$this->assert
			->sizeOf($iterator)->isEqualTo(2)
		;

		$innerInnerIterator = new tokenizer\iterator();
		$innerInnerIterator->append(new tokenizer\token(uniqid()));

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append(new tokenizer\token(uniqid()))
			->append($innerInnerIterator)
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append(new tokenizer\token(uniqid()))
			->append($innerIterator)
		;

		$innerIterator->append(new tokenizer\token(uniqid()));

		$this->assert
			->sizeOf($iterator)->isEqualTo(4)
		;

		$innerInnerIterator->append(new tokenizer\token(uniqid()));

		$this->assert
			->sizeOf($iterator)->isEqualTo(5)
		;
	}

	public function testRewind()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->variable($iterator->key())->isNull()
			->object($iterator->rewind())->isIdenticalTo($iterator)
			->variable($iterator->key())->isNull()
		;

		$iterator->append(new tokenizer\token(uniqid()));

		$this->assert
			->integer($iterator->key())->isZero()
			->object($iterator->rewind())->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
		;

		$iterator->append(new tokenizer\token(uniqid()))->next();

		$this->assert
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->rewind())->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
		;

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append(new tokenizer\token('1'))
			->append(new tokenizer\token('2'))
			->next();
		;

		$iterator->append($innerIterator)->end();

		$this->assert
			->integer($iterator->key())->isEqualTo(3)
			->integer($innerIterator->key())->isEqualTo(1)
			->object($iterator->rewind())->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
			->integer($innerIterator->key())->isEqualTo(1)
		;

		$iterator = new tokenizer\iterator();

		$iterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
			->append($token3 = new tokenizer\token(uniqid()))
			->skipValue($token1)
			->end()
		;

		$this->assert
			->integer($iterator->key())->isEqualTo(2)
			->object($iterator->current())->isIdenticalTo($token3)
			->object($iterator->rewind())->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($token2)
		;
	}

	public function testEnd()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->object($iterator->end())->isIdenticalTo($iterator)
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator->append($token1 = new tokenizer\token(uniqid()));

		$this->assert
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->end())->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
		;

		$iterator->append($token2 = new tokenizer\token(uniqid()));

		$this->assert
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->end())->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($token2)
		;

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append($token3 = new tokenizer\token(uniqid()))
			->append($token4 = new tokenizer\token(uniqid()))
		;

		$iterator->append($innerIterator);

		$this->assert
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($token2)
			->object($iterator->end())->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(3)
			->object($iterator->current())->isIdenticalTo($token4)
		;

		$iterator = new tokenizer\iterator();

		$iterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
			->append($token3 = new tokenizer\token(uniqid()))
			->skipValue($token3)
		;

		$this->assert
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->end())->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($token2)
		;
	}

	public function testAppend()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->object($iterator->append($token1 = new tokenizer\token(uniqid())))->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
		;

		$iterator = new tokenizer\iterator();

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->object($iterator->append($innerIterator))->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
		;

		$iterator->end()->next();

		$otherInnerIterator = new tokenizer\iterator();
		$otherInnerIterator
			->append($token3 = new tokenizer\token(uniqid()))
			->append($token4 = new tokenizer\token(uniqid()))
			->end()
		;

		$this->assert
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->integer($otherInnerIterator->key())->isEqualTo(1)
			->object($otherInnerIterator->current())->isIdenticalTo($token4)
			->object($iterator->append($otherInnerIterator))->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(2)
			->object($iterator->current())->isIdenticalTo($token3)
			->integer($otherInnerIterator->key())->isZero()
			->object($otherInnerIterator->current())->isIdenticalTo($token3)
		;

		$this->assert
			->exception(function() use ($iterator, $innerIterator) {
					$iterator->append($innerIterator);
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to append value because it has already a parent')
		;

		$iterator = new tokenizer\iterator();
		$iterator->skipValue($skipedValue = uniqid());

		$innerIterator = new tokenizer\iterator();

		$this->assert
			->object($iterator->append($innerIterator))->isIdenticalTo($iterator)
			->sizeOf($iterator)->isZero()
			->array($innerIterator->getSkipedValues())->isEmpty()
		;

		$iterator = new tokenizer\iterator();
		$iterator->skipValue($skipedValue = uniqid());

		$innerIterator = new tokenizer\iterator();
		$innerIterator->skipValue($skipedValue);

		$this->assert
			->object($iterator->append($innerIterator))->isIdenticalTo($iterator)
			->sizeOf($iterator)->isZero()
			->array($innerIterator->getSkipedValues())->isEqualTo(array($skipedValue))
		;

		$iterator = new tokenizer\iterator();
		$iterator->skipValue($skipedValue1 = uniqid());

		$iterator = new tokenizer\iterator();
		$iterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
			->append($token3 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->object($iterator->current())->isIdenticalTo($token1)
		;
	}

	public function testSkipValue()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->object($iterator->skipValue($skipedValue = uniqid()))->isIdenticalTo($iterator)
			->array($iterator->getSkipedValues())->isEqualTo(array($skipedValue))
			->object($iterator->skipValue($skipedValue))->isIdenticalTo($iterator)
			->array($iterator->getSkipedValues())->isEqualTo(array($skipedValue))
		;
	}

	public function testReset()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->sizeOf($iterator)->isZero()
			->object($iterator->reset())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->sizeOf($iterator)->isZero()
		;

		$iterator->append($token = new tokenizer\token(uniqid()));

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->variable($iterator->current())->isIdenticalTo($token)
			->sizeOf($iterator)->isEqualTo(1)
			->object($iterator->reset())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->sizeOf($iterator)->isZero()
		;
	}

	public function testGetValue()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->variable($iterator->getValue())->isNull()
		;

		$innerIterator = new tokenizer\iterator();
		$innerIterator->append(new tokenizer\token(uniqid()));

		$iterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($innerIterator)
			->append($token2 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->object($iterator->getValue())->isIdenticalTo($token1)
			->object($iterator->next()->getValue())->isIdenticalTo($innerIterator)
			->object($iterator->next()->getValue())->isIdenticalTo($token2)
			->variable($iterator->next()->getValue())->isNull()
		;
	}

	public function testGetParent()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->variable($iterator->getParent())->isNull()
		;

		$parentIterator = new tokenizer\iterator();
		$parentIterator->append($iterator);

		$this->assert
			->object($iterator->getParent())->isIdenticalTo($parentIterator)
		;
	}

	public function testGetRoot()
	{
		$childIterator = new tokenizer\iterator();

		$this->assert
			->variable($childIterator->getRoot())->isNull()
		;

		$parentIterator = new tokenizer\iterator();
		$parentIterator->append($childIterator);

		$this->assert
			->object($childIterator->getRoot())->isIdenticalTo($parentIterator)
		;

		$grandFatherIterator = new tokenizer\iterator();
		$grandFatherIterator->append($parentIterator);

		$this->assert
			->object($childIterator->getRoot())->isIdenticalTo($grandFatherIterator)
			->object($parentIterator->getRoot())->isIdenticalTo($grandFatherIterator)
		;
	}

	public function testSeek()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->object($iterator->seek(rand(0, PHP_INT_MAX)))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
		;

		$iterator->append($token1 = new tokenizer\token(uniqid()));

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->seek(0))->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->seek(rand(1, PHP_INT_MAX)))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator->rewind()->append($token2 = new tokenizer\token(uniqid()));

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->seek(0))->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->seek(1))->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($token2)
			->object($iterator->seek(rand(2, PHP_INT_MAX)))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator->rewind()->append($token3 = new tokenizer\token(uniqid()));

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
			->object($iterator->seek(2))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->object($iterator->current())->isIdenticalTo($token3)
			->object($iterator->seek(3))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->object($iterator->seek(1))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($token2)
			->object($iterator->rewind()->prev()->seek(1))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($token2)
			->object($iterator->rewind()->seek(2))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->object($iterator->current())->isIdenticalTo($token3)
			->object($iterator->seek(0))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->object($iterator->current())->isIdenticalTo($token1)
		;
	}

	public function testFindTag()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->variable($iterator->key())->isNull()
			->variable($iterator->findTag(uniqid()))->isNull()
			->variable($iterator->key())->isNull()
		;

		$iterator->append(new tokenizer\token($token = uniqid()));

		$this->assert
			->integer($iterator->key())->isZero()
			->variable($iterator->findTag(uniqid()))->isNull()
			->variable($iterator->key())->isNull()
			->integer($iterator->findTag($token))->isZero()
			->integer($iterator->key())->isZero()
		;
	}

	public function testGoToNextTagWhichIsNot()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->object($iterator->goToNextTagWhichIsNot(array()))->isIdenticalTo($iterator)
		;

		$iterator->append(new tokenizer\token(T_FUNCTION));

		$this->assert
			->integer($iterator->key())->isZero()
			->object($iterator->goToNextTagWhichIsNot(array(T_WHITESPACE)))->isIdenticalTo($iterator)
			->variable($iterator->key())->isNull()
		;

		$iterator->append(new tokenizer\token(T_WHITESPACE));

		$this->assert
			->object($iterator->rewind()->goToNextTagWhichIsNot(array(T_STRING)))->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->rewind()->goToNextTagWhichIsNot(array(T_WHITESPACE)))->isIdenticalTo($iterator)
			->variable($iterator->key())->isNull()
		;

		$iterator->append(new tokenizer\token(T_COMMENT));

		$this->assert
			->object($iterator->rewind()->goToNextTagWhichIsNot(array(T_STRING)))->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->rewind()->goToNextTagWhichIsNot(array(T_WHITESPACE)))->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(2)
			->object($iterator->rewind()->goToNextTagWhichIsNot(array(T_COMMENT)))->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->rewind()->goToNextTagWhichIsNot(array(T_WHITESPACE, T_COMMENT)))->isIdenticalTo($iterator)
			->variable($iterator->key())->isNull()
			->object($iterator->rewind()->goToNextTagWhichIsNot(array(T_COMMENT, T_WHITESPACE)))->isIdenticalTo($iterator)
			->variable($iterator->key())->isNull()
		;

		$iterator->append(new tokenizer\token(T_STRING));

		$this->assert
			->object($iterator->rewind()->goToNextTagWhichIsNot(array(T_STRING)))->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->rewind()->goToNextTagWhichIsNot(array(T_WHITESPACE)))->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(2)
			->object($iterator->rewind()->goToNextTagWhichIsNot(array(T_COMMENT)))->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->rewind()->goToNextTagWhichIsNot(array(T_WHITESPACE, T_COMMENT)))->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(3)
			->object($iterator->rewind()->goToNextTagWhichIsNot(array(T_COMMENT, T_WHITESPACE)))->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(3)
			->object($iterator->rewind()->goToNextTagWhichIsNot(array(T_COMMENT, T_WHITESPACE, T_STRING)))->isIdenticalTo($iterator)
			->variable($iterator->key())->isNull()
		;
	}
}
