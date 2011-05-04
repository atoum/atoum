<?php

namespace mageekguy\atoum\tests\units\php;

use
	\mageekguy\atoum,
	\mageekguy\atoum\php
;

require_once(__DIR__ . '/../../runner.php');

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

	public function testValid()
	{
		$iterator = new php\iterator();

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
		$iterator = new php\iterator();

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

		$innerIterator = new php\iterator();
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
		$iterator = new php\iterator();

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

		$innerIterator = new php\iterator();
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

	public function testNext()
	{
		$iterator = new php\iterator();

		$this->assert
			->boolean($iterator->valid())->isFalse()
			->object($iterator->next())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
		;

		$iterator->append(uniqid());

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(0)
			->object($iterator->next())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
		;
	}

	public function testCount()
	{
		$iterator = new php\iterator();

		$this->assert
			->sizeOf($iterator)->isEqualTo(0)
		;

		$iterator->append(uniqid());

		$this->assert
			->sizeOf($iterator)->isEqualTo(1)
		;

		$innerIterator = new php\iterator();

		$iterator->append($innerIterator);

		$this->assert
			->sizeOf($iterator)->isEqualTo(1)
		;

		$innerIterator->append(uniqid());

		$this->assert
			->sizeOf($iterator)->isEqualTo(2)
		;
	}
}

?>
