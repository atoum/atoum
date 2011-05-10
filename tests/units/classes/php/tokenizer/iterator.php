<?php

namespace mageekguy\atoum\tests\units\php\tokenizer;

use
	\mageekguy\atoum,
	\mageekguy\atoum\php\tokenizer
;

require_once(__DIR__ . '/../../../runner.php');

class iterator extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->hasInterface('\Iterator')
				->hasInterface('\Countable')
		;
	}

	public function test__construct()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->array($iterator->getExcludedValues())->isEmpty()
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

		$iterator->append($value1 = uniqid());

		$this->assert
			->integer($iterator->key())->isZero()
			->castToString($iterator)->isEqualTo($value1)
			->integer($iterator->key())->isZero()
		;

		$iterator->append($value2 = rand(1, PHP_INT_MAX))->end();

		$this->assert
			->integer($iterator->key())->isEqualTo(1)
			->castToString($iterator)->isEqualTo($value1 . $value2)
			->integer($iterator->key())->isZero()
		;

		$innerInnerIterator = new tokenizer\iterator();
		$innerInnerIterator->append($value3 = uniqid());

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append($value2 = uniqid())
			->append($innerInnerIterator)
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append($value1 = uniqid())
			->append($innerIterator)
		;

		$this->assert
			->castToString($iterator)->isEqualTo($value1 . $value2 . $value3)
		;
	}

	public function testValid()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->boolean($iterator->valid())->isFalse()
		;

		$iterator->append($value = uniqid());

		$this->assert
			->boolean($iterator->valid())->isTrue()
		;
	}

	public function testKey()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->variable($iterator->key())->isNull()
		;

		$iterator->append(uniqid());

		$this->assert
			->integer($iterator->key())->isZero()
		;

		$iterator->next();

		$this->assert
			->variable($iterator->key())->isNull()
		;

		$iterator->append(uniqid());

		$this->assert
			->integer($iterator->key())->isEqualTo(1)
		;

		$iterator->next();

		$this->assert
			->variable($iterator->key())->isNull()
		;

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append(uniqid())
			->append(uniqid())
			->append(uniqid())
		;

		$iterator
			->append($innerIterator)
			->append(uniqid())
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

		$iterator->append($value1 = uniqid());

		$this->assert
			->string($iterator->current())->isEqualTo($value1)
			->variable($iterator->next()->current())->isNull()
		;

		$iterator->append($value2 = uniqid());

		$this->assert
			->string($iterator->current())->isEqualTo($value2)
			->variable($iterator->next()->current())->isNull()
		;

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append($value3 = uniqid())
			->append($value4 = uniqid())
			->append($value5 = uniqid())
		;

		$iterator
			->append($innerIterator)
			->append($value6 = uniqid())
		;

		$iterator->rewind();

		$this->assert
			->string($iterator->current())->isEqualTo($value1)
			->string($iterator->next()->current())->isEqualTo($value2)
			->string($iterator->next()->current())->isEqualTo($value3)
			->string($iterator->next()->current())->isEqualTo($value4)
			->string($iterator->next()->current())->isEqualTo($value5)
			->string($iterator->next()->current())->isEqualTo($value6)
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

		$iterator->append($value1 = uniqid());

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator->append($value2 = uniqid())->end();

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->string($iterator->current())->isEqualTo($value2)
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
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
			->string($iterator->current())->isEqualTo($value2)
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator = new tokenizer\iterator();

		$iterator = new tokenizer\iterator();
		$iterator
			->append($value1 = uniqid())
			->append($value2 = uniqid())
			->append($value3 = uniqid())
			->end()
			->excludeValue($value2)
		;

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->string($iterator->current())->isEqualTo($value3)
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(0)
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->prev())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append($value1 = uniqid())
			->append($value2 = uniqid())
			->append($value3 = uniqid())
			->end()
		;

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->string($iterator->current())->isEqualTo($value3)
			->object($iterator->prev(1))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->string($iterator->current())->isEqualTo($value2)
			->object($iterator->end()->prev(2))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->end()->prev(3))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append($value1 = uniqid())
			->append($value2 = uniqid())
			->append($value3 = uniqid())
			->append($value4 = uniqid())
			->end()
			->excludeValue($value2)
		;

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(3)
			->string($iterator->current())->isEqualTo($value4)
			->object($iterator->prev(1))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->string($iterator->current())->isEqualTo($value3)
			->object($iterator->end()->prev(2))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
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

		$iterator->append($value1 = uniqid());

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(0)
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->next())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator
			->append(new tokenizer\iterator())
			->append($value2 = uniqid())
			->rewind();
		;

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(0)
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->next())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->string($iterator->current())->isEqualTo($value2)
			->object($iterator->next())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append($value1 = uniqid())
			->append($value2 = uniqid())
			->append($value3 = uniqid())
			->excludeValue($value2)
		;

		$this->assert
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->next())->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(2)
			->string($iterator->current())->isEqualTo($value3)
			->object($iterator->next())->isIdenticalTo($iterator)
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append($value1 = uniqid())
			->append($value2 = uniqid())
			->append($value3 = uniqid())
		;

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->next(1))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->string($iterator->current())->isEqualTo($value2)
			->object($iterator->rewind()->next(2))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->string($iterator->current())->isEqualTo($value3)
			->object($iterator->rewind()->next(rand(3, PHP_INT_MAX)))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append($value1 = uniqid())
			->append($value2 = uniqid())
			->append($value3 = uniqid())
			->append($value4 = uniqid())
			->excludeValue($value2)
		;

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->next(1))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->string($iterator->current())->isEqualTo($value3)
			->object($iterator->rewind()->next(2))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(3)
			->string($iterator->current())->isEqualTo($value4)
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

		$iterator->append(uniqid());

		$this->assert
			->sizeOf($iterator)->isEqualTo(1)
		;

		$innerIterator = new tokenizer\iterator();

		$iterator->append($innerIterator);

		$this->assert
			->sizeOf($iterator)->isEqualTo(1)
		;

		$innerIterator->append(uniqid());

		$this->assert
			->sizeOf($iterator)->isEqualTo(2)
		;

		$innerInnerIterator = new tokenizer\iterator();
		$innerInnerIterator->append(uniqid());

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append(uniqid())
			->append($innerInnerIterator)
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append(uniqid())
			->append($innerIterator)
		;

		$innerIterator->append(uniqid());

		$this->assert
			->sizeOf($iterator)->isEqualTo(4)
		;

		$innerInnerIterator->append(uniqid());

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

		$iterator->append(uniqid());

		$this->assert
			->integer($iterator->key())->isZero()
			->object($iterator->rewind())->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
		;

		$iterator->append(uniqid())->next();

		$this->assert
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->rewind())->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
		;

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append(uniqid())
			->append(uniqid())
			->next();
		;

		$iterator->append($innerIterator)->end();

		$this->assert
			->integer($iterator->key())->isEqualTo(3)
			->object($iterator->rewind())->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
			->integer($innerIterator->key())->isEqualTo(1)
		;

		$iterator = new tokenizer\iterator();

		$iterator
			->append($value1 = uniqid())
			->append($value2 = uniqid())
			->append($value3 = uniqid())
			->excludeValue($value1)
			->end()
		;

		$this->assert
			->integer($iterator->key())->isEqualTo(2)
			->string($iterator->current())->isEqualTo($value3)
			->object($iterator->rewind())->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(1)
			->string($iterator->current())->isEqualTo($value2)
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

		$iterator->append($value1 = uniqid());

		$this->assert
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->end())->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
		;

		$iterator->append($value2 = uniqid());

		$this->assert
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->end())->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(1)
			->string($iterator->current())->isEqualTo($value2)
		;

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append($value3 = uniqid())
			->append($value4 = uniqid())
		;

		$iterator->append($innerIterator);

		$this->assert
			->integer($iterator->key())->isEqualTo(1)
			->string($iterator->current())->isEqualTo($value2)
			->object($iterator->end())->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(3)
			->string($iterator->current())->isEqualTo($value4)
		;

		$iterator = new tokenizer\iterator();

		$iterator
			->append($value1 = uniqid())
			->append($value2 = uniqid())
			->append($value3 = uniqid())
			->excludeValue($value3)
		;

		$this->assert
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->end())->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(1)
			->string($iterator->current())->isEqualTo($value2)
		;
	}

	public function testAppend()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->object($iterator->append($value1 = uniqid()))->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
		;

		$iterator = new tokenizer\iterator();

		$innerIterator = new tokenizer\iterator();
		$innerIterator
			->append($value1 = uniqid())
			->append($value2 = uniqid())
		;

		$this->assert
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->object($iterator->append($innerIterator))->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
		;

		$iterator->end()->next();

		$otherInnerIterator = new tokenizer\iterator();
		$otherInnerIterator
			->append($value3 = uniqid())
			->append($value4 = uniqid())
			->end()
		;

		$this->assert
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->integer($otherInnerIterator->key())->isEqualTo(1)
			->string($otherInnerIterator->current())->isEqualTo($value4)
			->object($iterator->append($otherInnerIterator))->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(2)
			->string($iterator->current())->isEqualTo($value3)
			->integer($otherInnerIterator->key())->isZero()
			->string($otherInnerIterator->current())->isEqualTo($value3)
		;

		$this->assert
			->exception(function() use ($iterator, $innerIterator) {
					$iterator->append($innerIterator);
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to append iterator, it has already a parent')
		;

		$iterator = new tokenizer\iterator();
		$iterator->excludeValue($excludedValue = uniqid());

		$innerIterator = new tokenizer\iterator();

		$this->assert
			->object($iterator->append($innerIterator))->isIdenticalTo($iterator)
			->sizeOf($iterator)->isZero()
			->array($innerIterator->getExcludedValues())->isEqualTo($iterator->getExcludedValues())
		;

		$iterator = new tokenizer\iterator();
		$iterator->excludeValue($excludedValue = uniqid());

		$innerIterator = new tokenizer\iterator();
		$innerIterator->excludeValue($excludedValue);

		$this->assert
			->object($iterator->append($innerIterator))->isIdenticalTo($iterator)
			->sizeOf($iterator)->isZero()
			->array($innerIterator->getExcludedValues())->isEqualTo($iterator->getExcludedValues())
		;

		$iterator = new tokenizer\iterator();
		$iterator->excludeValue($excludedValue1 = uniqid());

		$innerIterator = new tokenizer\iterator();
		$innerIterator->excludeValue($excludedValue2 = uniqid());

		$this->assert
			->object($iterator->append($innerIterator))->isIdenticalTo($iterator)
			->sizeOf($iterator)->isZero()
			->array($innerIterator->getExcludedValues())->isEqualTo(array($excludedValue1, $excludedValue2))
		;

		$iterator = new tokenizer\iterator();
		$iterator
			->append($value1 = uniqid())
			->append($value2 = uniqid())
			->append($value3 = uniqid())
		;

		$this->assert
			->string($iterator->current())->isEqualTo($value1)
		;
	}

	public function testExcludeValue()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->object($iterator->excludeValue($excludedValue = uniqid()))->isIdenticalTo($iterator)
			->array($iterator->getExcludedValues())->isEqualTo(array($excludedValue))
			->object($iterator->excludeValue($excludedValue))->isIdenticalTo($iterator)
			->array($iterator->getExcludedValues())->isEqualTo(array($excludedValue))
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

		$iterator->append($value = uniqid());

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->variable($iterator->current())->isEqualTo($value)
			->sizeOf($iterator)->isEqualTo(1)
			->object($iterator->reset())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->sizeOf($iterator)->isZero()
		;
	}

	public function testGetInnerIterator()
	{
		$iterator = new tokenizer\iterator();

		$this->assert
			->variable($iterator->getInnerIterator())->isNull()
		;

		$innerIterator = new tokenizer\iterator();
		$innerIterator->append(uniqid());

		$iterator
			->append(uniqid())
			->append($innerIterator)
			->append(uniqid())
		;

		$this->assert
			->variable($iterator->getInnerIterator())->isNull()
			->object($iterator->next()->getInnerIterator())->isIdenticalTo($innerIterator)
			->variable($iterator->next()->getInnerIterator())->isNull()
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

		$iterator->append($value1 = uniqid());

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->seek(0))->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->seek(rand(1, PHP_INT_MAX)))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator->rewind()->append($value2 = uniqid());

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->seek(0))->isIdenticalTo($iterator)
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->seek(1))->isIdenticalTo($iterator)
			->integer($iterator->key())->isEqualTo(1)
			->string($iterator->current())->isEqualTo($value2)
			->object($iterator->seek(rand(2, PHP_INT_MAX)))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
		;

		$iterator->rewind()->append($value3 = uniqid());

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->string($iterator->current())->isEqualTo($value1)
			->object($iterator->seek(2))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->string($iterator->current())->isEqualTo($value3)
			->object($iterator->seek(3))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->variable($iterator->current())->isNull()
			->object($iterator->seek(1))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->string($iterator->current())->isEqualTo($value2)
			->object($iterator->rewind()->prev()->seek(1))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->variable($iterator->current())->isEqualTo($value2)
			->object($iterator->rewind()->seek(2))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(2)
			->string($iterator->current())->isEqualTo($value3)
			->object($iterator->seek(0))->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
			->variable($iterator->current())->isEqualTo($value1)
		;
	}
}

?>
