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
		$this
			->testedClass
				->implements('Iterator')
				->implements('Countable')
		;
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->array($this->testedInstance->getSkipedValues())->isEmpty()
				->sizeOf($this->testedInstance)->isZero()
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->current())->isNull()
				->variable($this->testedInstance->key())->isNull()
		;
	}

	public function test__toString()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->castToString($this->testedInstance)->isEmpty()
			->if($this->testedInstance->append($token1 = new tokenizer\token(uniqid())))
			->then
				->integer($this->testedInstance->key())->isZero()
				->castToString($this->testedInstance)->isEqualTo($token1)
				->integer($this->testedInstance->key())->isZero()
			->if($this->testedInstance->append($token2 = new tokenizer\token(uniqid(), rand(1, PHP_INT_MAX)))->end())
			->then
				->integer($this->testedInstance->key())->isEqualTo(1)
				->castToString($this->testedInstance)->isEqualTo($token1 . $token2)
				->integer($this->testedInstance->key())->isEqualTo(1)
			->if(
				$innerInnerIterator = new tokenizer\iterator(),
				$innerInnerIterator->append($token3 = new tokenizer\token(uniqid())),
				$innerIterator = new tokenizer\iterator(),
				$innerIterator
					->append($token2 = new tokenizer\token(uniqid()))
					->append($innerInnerIterator),
				$this->newTestedInstance,
				$this->testedInstance
					->append($token1 = new tokenizer\token(uniqid()))
					->append($innerIterator)
			)
			->then
				->castToString($this->testedInstance)->isEqualTo($token1 . $token2 . $token3)
		;
	}

	public function testValid()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->boolean($this->testedInstance->valid())->isFalse()
			->if($this->testedInstance->append(new tokenizer\token(uniqid())))
			->then
				->boolean($this->testedInstance->valid())->isTrue()
			->if($this->testedInstance->next())
			->then
				->boolean($this->testedInstance->valid())->isFalse()
		;
	}

	public function testKey()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->key())->isNull()
			->if($this->testedInstance->append(new tokenizer\token(uniqid())))
			->then
				->integer($this->testedInstance->key())->isZero()
			->if($this->testedInstance->next())
			->then
				->variable($this->testedInstance->key())->isNull()
			->if($this->testedInstance->append(new tokenizer\token(uniqid())))
			->then
				->integer($this->testedInstance->key())->isEqualTo(1)
			->if($this->testedInstance->next())
			->then
				->variable($this->testedInstance->key())->isNull()
			->if(
				$innerIterator = new tokenizer\iterator(),
				$innerIterator
					->append(new tokenizer\token(uniqid()))
					->append(new tokenizer\token(uniqid()))
					->append(new tokenizer\token(uniqid())),
				$this->testedInstance
					->append($innerIterator)
					->append(new tokenizer\token(uniqid())),
				$this->testedInstance->rewind()
			)
			->then
				->integer($this->testedInstance->key())->isEqualTo(0)
				->integer($this->testedInstance->next()->key())->isEqualTo(1)
				->integer($this->testedInstance->next()->key())->isEqualTo(2)
				->integer($this->testedInstance->next()->key())->isEqualTo(3)
				->integer($this->testedInstance->next()->key())->isEqualTo(4)
				->integer($this->testedInstance->next()->key())->isEqualTo(5)
				->variable($this->testedInstance->next()->key())->isNull()
		;
	}

	public function testCurrent()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->current())->isNull()
			->if($this->testedInstance->append($token1 = new tokenizer\token(uniqid())))
			->then
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->variable($this->testedInstance->next()->current())->isNull()
			->if($this->testedInstance->append($token2 = new tokenizer\token(uniqid())))
			->then
				->object($this->testedInstance->current())->isIdenticalTo($token2)
				->variable($this->testedInstance->next()->current())->isNull()
			->if(
				$innerIterator = new tokenizer\iterator(),
				$innerIterator
					->append($token3 = new tokenizer\token(uniqid()))
					->append($token4 = new tokenizer\token(uniqid()))
					->append($token5 = new tokenizer\token(uniqid())),
				$this->testedInstance
					->append($innerIterator)
					->append($token6 = new tokenizer\token(uniqid())),
				$this->testedInstance->rewind()
			)
			->then
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->next()->current())->isIdenticalTo($token2)
				->object($this->testedInstance->next()->current())->isIdenticalTo($token3)
				->object($this->testedInstance->next()->current())->isIdenticalTo($token4)
				->object($this->testedInstance->next()->current())->isIdenticalTo($token5)
				->object($this->testedInstance->next()->current())->isIdenticalTo($token6)
				->variable($this->testedInstance->next()->current())->isNull()
		;
	}

	public function testPrev()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
				->object($this->testedInstance->prev())->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if($this->testedInstance->append($token1 = new tokenizer\token(uniqid())))
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->prev())->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if($this->testedInstance->append($token2 = new tokenizer\token(uniqid()))->end())
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->current())->isIdenticalTo($token2)
				->object($this->testedInstance->prev())->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->prev())->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if(
				$innerIterator = new tokenizer\iterator(),
				$this->testedInstance->append($innerIterator)->end()
			)
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->current())->isIdenticalTo($token2)
				->object($this->testedInstance->prev())->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->prev())->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if(
				$this->newTestedInstance,
				$this->testedInstance
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
					->append($token3 = new tokenizer\token(uniqid()))
					->end()
					->skipValue($token2)
			)
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(2)
				->object($this->testedInstance->current())->isIdenticalTo($token3)
				->object($this->testedInstance->prev())->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(0)
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->prev())->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if(
				$this->newTestedInstance,
				$this->testedInstance
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
					->append($token3 = new tokenizer\token(uniqid()))
					->end()
			)
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(2)
				->object($this->testedInstance->current())->isIdenticalTo($token3)
				->object($this->testedInstance->prev(1))->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->current())->isIdenticalTo($token2)
				->object($this->testedInstance->end()->prev(2))->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->end()->prev(3))->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if(
				$this->newTestedInstance,
				$this->testedInstance
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
					->append($token3 = new tokenizer\token(uniqid()))
					->append($token4 = new tokenizer\token(uniqid()))
					->end()
					->skipValue($token2)
			)
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(3)
				->object($this->testedInstance->current())->isIdenticalTo($token4)
				->object($this->testedInstance->prev(1))->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(2)
				->object($this->testedInstance->current())->isIdenticalTo($token3)
				->object($this->testedInstance->end()->prev(2))->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->end()->prev(rand(3, PHP_INT_MAX)))->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
		;
	}

	public function testNext()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
				->object($this->testedInstance->next())->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
				->object($this->testedInstance->next(rand(2, PHP_INT_MAX)))->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if($this->testedInstance->append($token1 = new tokenizer\token(uniqid())))
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(0)
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->next())->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if(
				$this->testedInstance
					->append(new tokenizer\iterator())
					->append($token2 = new tokenizer\token(uniqid()))
					->rewind()
			)
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(0)
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->next())->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->current())->isIdenticalTo($token2)
				->object($this->testedInstance->next())->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if(
				$this->newTestedInstance,
				$this->testedInstance
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
					->append($token3 = new tokenizer\token(uniqid()))
					->skipValue($token2)
			)
			->then
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->next())->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(2)
				->object($this->testedInstance->current())->isIdenticalTo($token3)
				->object($this->testedInstance->next())->isTestedInstance
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if(
				$this->newTestedInstance,
				$this->testedInstance
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
					->append($token3 = new tokenizer\token(uniqid()))
			)
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->next(1))->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->current())->isIdenticalTo($token2)
				->object($this->testedInstance->rewind()->next(2))->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(2)
				->object($this->testedInstance->current())->isIdenticalTo($token3)
				->object($this->testedInstance->rewind()->next(rand(3, PHP_INT_MAX)))->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if(
				$this->newTestedInstance,
				$this->testedInstance
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
					->append($token3 = new tokenizer\token(uniqid()))
					->append($token4 = new tokenizer\token(uniqid()))
					->skipValue($token2)
			)
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->next(1))->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(2)
				->object($this->testedInstance->current())->isIdenticalTo($token3)
				->object($this->testedInstance->rewind()->next(2))->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(3)
				->object($this->testedInstance->current())->isIdenticalTo($token4)
				->object($this->testedInstance->rewind()->next(rand(3, PHP_INT_MAX)))->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
		;
	}

	public function testCount()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->sizeOf($this->testedInstance)->isEqualTo(0)
			->if($this->testedInstance->append(new tokenizer\token(uniqid())))
			->then
				->sizeOf($this->testedInstance)->isEqualTo(1)
			->if(
				$innerIterator = new tokenizer\iterator(),
				$this->testedInstance->append($innerIterator)
			)
			->then
				->sizeOf($this->testedInstance)->isEqualTo(1)
			->if($innerIterator->append(new tokenizer\token(uniqid())))
			->then
				->sizeOf($this->testedInstance)->isEqualTo(2)
			->if(
				$innerInnerIterator = new tokenizer\iterator(),
				$innerInnerIterator->append(new tokenizer\token(uniqid())),
				$innerIterator = new tokenizer\iterator(),
				$innerIterator
					->append(new tokenizer\token(uniqid()))
					->append($innerInnerIterator),
				$this->newTestedInstance,
				$this->testedInstance
					->append(new tokenizer\token(uniqid()))
					->append($innerIterator),
				$innerIterator->append(new tokenizer\token(uniqid()))
			)
			->then
				->sizeOf($this->testedInstance)->isEqualTo(4)
			->if($innerInnerIterator->append(new tokenizer\token(uniqid())))
			->then
				->sizeOf($this->testedInstance)->isEqualTo(5)
		;
	}

	public function testRewind()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->key())->isNull()
				->object($this->testedInstance->rewind())->isTestedInstance
				->variable($this->testedInstance->key())->isNull()
			->if($this->testedInstance->append(new tokenizer\token(uniqid())))
			->then
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->rewind())->isTestedInstance
				->integer($this->testedInstance->key())->isZero()
			->if($this->testedInstance->append(new tokenizer\token(uniqid()))->next())
			->then
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->rewind())->isTestedInstance
				->integer($this->testedInstance->key())->isZero()
			->if(
				$innerIterator = new tokenizer\iterator(),
				$innerIterator
					->append(new tokenizer\token('1'))
					->append(new tokenizer\token('2'))
					->next(),
				$this->testedInstance->append($innerIterator)->end()
			)
			->then
				->integer($this->testedInstance->key())->isEqualTo(3)
				->integer($innerIterator->key())->isEqualTo(1)
				->object($this->testedInstance->rewind())->isTestedInstance
				->integer($this->testedInstance->key())->isZero()
				->integer($innerIterator->key())->isEqualTo(1)
			->if(
				$this->newTestedInstance,
				$this->testedInstance
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
					->append($token3 = new tokenizer\token(uniqid()))
					->skipValue($token1)
					->end()
			)
			->then
				->integer($this->testedInstance->key())->isEqualTo(2)
				->object($this->testedInstance->current())->isIdenticalTo($token3)
				->object($this->testedInstance->rewind())->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->current())->isIdenticalTo($token2)
		;
	}

	public function testEnd()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
				->object($this->testedInstance->end())->isTestedInstance
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if($this->testedInstance->append($token1 = new tokenizer\token(uniqid())))
			->then
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->end())->isTestedInstance
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
			->if($this->testedInstance->append($token2 = new tokenizer\token(uniqid())))
			->then
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->end())->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->current())->isIdenticalTo($token2)
			->if(
				$innerIterator = new tokenizer\iterator(),
				$innerIterator
					->append($token3 = new tokenizer\token(uniqid()))
					->append($token4 = new tokenizer\token(uniqid())),
				$this->testedInstance->append($innerIterator)
			)
			->then
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->current())->isIdenticalTo($token2)
				->object($this->testedInstance->end())->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(3)
				->object($this->testedInstance->current())->isIdenticalTo($token4)
			->if(
				$this->newTestedInstance,
				$this->testedInstance
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
					->append($token3 = new tokenizer\token(uniqid()))
					->skipValue($token3)
			)
			->then
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->end())->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->current())->isIdenticalTo($token2)
		;
	}

	public function testAppend()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
				->object($this->testedInstance->append($token1 = new tokenizer\token(uniqid())))->isTestedInstance
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
			->if(
				$iterator = $this->newTestedInstance,
				$innerIterator = new tokenizer\iterator(),
				$innerIterator
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
			)
			->then
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
				->object($this->testedInstance->append($innerIterator))->isTestedInstance
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
			->if(
				$this->testedInstance->end()->next(),
				$otherInnerIterator = new tokenizer\iterator(),
				$otherInnerIterator
					->append($token3 = new tokenizer\token(uniqid()))
					->append($token4 = new tokenizer\token(uniqid()))
					->end()
			)
			->then
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
				->integer($otherInnerIterator->key())->isEqualTo(1)
				->object($otherInnerIterator->current())->isIdenticalTo($token4)
				->object($this->testedInstance->append($otherInnerIterator))->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(2)
				->object($this->testedInstance->current())->isIdenticalTo($token3)
				->integer($otherInnerIterator->key())->isZero()
				->object($otherInnerIterator->current())->isIdenticalTo($token3)
				->exception(function() use ($iterator, $innerIterator) {
						$iterator->append($innerIterator);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to append value because it has already a parent')
			->if(
				$this->newTestedInstance,
				$this->testedInstance->skipValue($skipedValue = uniqid()),
				$innerIterator = new tokenizer\iterator()
			)
			->then
				->object($this->testedInstance->append($innerIterator))->isTestedInstance
				->sizeOf($this->testedInstance)->isZero()
				->array($innerIterator->getSkipedValues())->isEmpty()
			->if(
				$this->newTestedInstance,
				$this->testedInstance->skipValue($skipedValue = uniqid()),
				$innerIterator = new tokenizer\iterator(),
				$innerIterator->skipValue($skipedValue)
			)
			->then
				->object($this->testedInstance->append($innerIterator))->isTestedInstance
				->sizeOf($this->testedInstance)->isZero()
				->array($innerIterator->getSkipedValues())->isEqualTo(array($skipedValue))
			->if(
				$this->newTestedInstance,
				$this->testedInstance->skipValue($skipedValue1 = uniqid()),
				$this->newTestedInstance,
				$this->newTestedInstance
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
					->append($token3 = new tokenizer\token(uniqid()))
			)
			->then
				->object($this->testedInstance->current())->isIdenticalTo($token1)
		;
	}

	public function testSkipValue()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->skipValue($skipedValue = uniqid()))->isTestedInstance
				->array($this->testedInstance->getSkipedValues())->isEqualTo(array($skipedValue))
				->object($this->testedInstance->skipValue($skipedValue))->isTestedInstance
				->array($this->testedInstance->getSkipedValues())->isEqualTo(array($skipedValue))
		;
	}

	public function testReset()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
				->sizeOf($this->testedInstance)->isZero()
				->object($this->testedInstance->reset())->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
				->sizeOf($this->testedInstance)->isZero()
			->if($this->testedInstance->append($token = new tokenizer\token(uniqid())))
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isZero()
				->variable($this->testedInstance->current())->isIdenticalTo($token)
				->sizeOf($this->testedInstance)->isEqualTo(1)
				->object($this->testedInstance->reset())->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
				->sizeOf($this->testedInstance)->isZero()
		;
	}

	public function testGetValue()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getValue())->isNull()
			->if(
				$innerIterator = new tokenizer\iterator(),
				$innerIterator->append(new tokenizer\token(uniqid())),
				$this->testedInstance
					->append($token1 = new tokenizer\token(uniqid()))
					->append($innerIterator)
					->append($token2 = new tokenizer\token(uniqid()))
			)
			->then
				->object($this->testedInstance->getValue())->isIdenticalTo($token1)
				->object($this->testedInstance->next()->getValue())->isIdenticalTo($innerIterator)
				->object($this->testedInstance->next()->getValue())->isIdenticalTo($token2)
				->variable($this->testedInstance->next()->getValue())->isNull()
		;
	}

	public function testGetParent()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getParent())->isNull()
			->if(
				$parentIterator = new tokenizer\iterator(),
				$parentIterator->append($this->testedInstance)
			)
			->then
				->object($this->testedInstance->getParent())->isIdenticalTo($parentIterator)
		;
	}

	public function testGetRoot()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getRoot())->isNull()
			->if(
				$parentIterator = new tokenizer\iterator(),
				$parentIterator->append($this->testedInstance)
			)
			->then
				->object($this->testedInstance->getRoot())->isIdenticalTo($parentIterator)
			->if(
				$grandFatherIterator = new tokenizer\iterator(),
				$grandFatherIterator->append($parentIterator)
			)
			->then
				->object($this->testedInstance->getRoot())->isIdenticalTo($grandFatherIterator)
				->object($parentIterator->getRoot())->isIdenticalTo($grandFatherIterator)
		;
	}

	public function testSeek()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
				->object($this->testedInstance->seek(rand(0, PHP_INT_MAX)))->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
			->if($this->testedInstance->append($token1 = new tokenizer\token(uniqid())))
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->seek(0))->isTestedInstance
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->seek(rand(1, PHP_INT_MAX)))->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if($this->testedInstance->rewind()->append($token2 = new tokenizer\token(uniqid())))
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->seek(0))->isTestedInstance
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->seek(1))->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->current())->isIdenticalTo($token2)
				->object($this->testedInstance->seek(rand(2, PHP_INT_MAX)))->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
			->if($this->testedInstance->rewind()->append($token3 = new tokenizer\token(uniqid())))
			->then
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
				->object($this->testedInstance->seek(2))->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(2)
				->object($this->testedInstance->current())->isIdenticalTo($token3)
				->object($this->testedInstance->seek(3))->isTestedInstance
				->boolean($this->testedInstance->valid())->isFalse()
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->current())->isNull()
				->object($this->testedInstance->seek(1))->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->current())->isIdenticalTo($token2)
				->object($this->testedInstance->rewind()->prev()->seek(1))->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->current())->isIdenticalTo($token2)
				->object($this->testedInstance->rewind()->seek(2))->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isEqualTo(2)
				->object($this->testedInstance->current())->isIdenticalTo($token3)
				->object($this->testedInstance->seek(0))->isTestedInstance
				->boolean($this->testedInstance->valid())->isTrue()
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->current())->isIdenticalTo($token1)
		;
	}

	public function testFindTag()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->key())->isNull()
				->variable($this->testedInstance->findTag(uniqid()))->isNull()
				->variable($this->testedInstance->key())->isNull()
			->if($this->testedInstance->append(new tokenizer\token($token = uniqid())))
			->then
				->integer($this->testedInstance->key())->isZero()
				->variable($this->testedInstance->findTag(uniqid()))->isNull()
				->variable($this->testedInstance->key())->isNull()
				->integer($this->testedInstance->findTag($token))->isZero()
				->integer($this->testedInstance->key())->isZero()
		;
	}

	public function testGoToNextTagWhichIsNot()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->goToNextTagWhichIsNot(array()))->isTestedInstance
			->if($this->testedInstance->append(new tokenizer\token(T_FUNCTION)))
			->then
				->integer($this->testedInstance->key())->isZero()
				->object($this->testedInstance->goToNextTagWhichIsNot(array(T_WHITESPACE)))->isTestedInstance
				->variable($this->testedInstance->key())->isNull()
			->if($this->testedInstance->append(new tokenizer\token(T_WHITESPACE)))
			->then
				->object($this->testedInstance->rewind()->goToNextTagWhichIsNot(array(T_STRING)))->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->rewind()->goToNextTagWhichIsNot(array(T_WHITESPACE)))->isTestedInstance
				->variable($this->testedInstance->key())->isNull()
			->if($this->testedInstance->append(new tokenizer\token(T_COMMENT)))
			->then
				->object($this->testedInstance->rewind()->goToNextTagWhichIsNot(array(T_STRING)))->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->rewind()->goToNextTagWhichIsNot(array(T_WHITESPACE)))->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(2)
				->object($this->testedInstance->rewind()->goToNextTagWhichIsNot(array(T_COMMENT)))->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->rewind()->goToNextTagWhichIsNot(array(T_WHITESPACE, T_COMMENT)))->isTestedInstance
				->variable($this->testedInstance->key())->isNull()
				->object($this->testedInstance->rewind()->goToNextTagWhichIsNot(array(T_COMMENT, T_WHITESPACE)))->isTestedInstance
				->variable($this->testedInstance->key())->isNull()
			->if($this->testedInstance->append(new tokenizer\token(T_STRING)))
			->then
				->object($this->testedInstance->rewind()->goToNextTagWhichIsNot(array(T_STRING)))->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->rewind()->goToNextTagWhichIsNot(array(T_WHITESPACE)))->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(2)
				->object($this->testedInstance->rewind()->goToNextTagWhichIsNot(array(T_COMMENT)))->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(1)
				->object($this->testedInstance->rewind()->goToNextTagWhichIsNot(array(T_WHITESPACE, T_COMMENT)))->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(3)
				->object($this->testedInstance->rewind()->goToNextTagWhichIsNot(array(T_COMMENT, T_WHITESPACE)))->isTestedInstance
				->integer($this->testedInstance->key())->isEqualTo(3)
				->object($this->testedInstance->rewind()->goToNextTagWhichIsNot(array(T_COMMENT, T_WHITESPACE, T_STRING)))->isTestedInstance
				->variable($this->testedInstance->key())->isNull()
		;
	}
}
