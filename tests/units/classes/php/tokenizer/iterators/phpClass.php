<?php

namespace mageekguy\atoum\tests\units\php\tokenizer\iterators;

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizer\iterators
;

require_once __DIR__ . '/../../../../runner.php';

class phpClass extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubClassOf('mageekguy\atoum\php\tokenizer\iterator')
		;
	}

	public function test__construct()
	{
		$iterator = new iterators\phpClass();

		$this->assert
			->array($iterator->getConstants())->isEmpty()
			->array($iterator->getMethods())->isEmpty()
		;
	}

	public function testAppendConstant()
	{
		$iterator = new iterators\phpClass();

		$constantIterator = new iterators\phpConstant();
		$constantIterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->object($iterator->appendConstant($constantIterator))->isIdenticalTo($iterator)
			->array($iterator->getConstants())->isEqualTo(array($constantIterator))
			->castToString($iterator)->isEqualTo($token1 . $token2)
		;
	}

	public function testAppendMethod()
	{
		$iterator = new iterators\phpClass();

		$methodIterator = new iterators\phpMethod();
		$methodIterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->object($iterator->appendMethod($methodIterator))->isIdenticalTo($iterator)
			->array($iterator->getMethods())->isEqualTo(array($methodIterator))
			->castToString($iterator)->isEqualTo($token1 . $token2)
		;
	}

	public function testAppendProperty()
	{
		$iterator = new iterators\phpClass();

		$propertyIterator = new iterators\phpProperty();
		$propertyIterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->object($iterator->appendProperty($propertyIterator))->isIdenticalTo($iterator)
			->array($iterator->getProperties())->isEqualTo(array($propertyIterator))
			->castToString($iterator)->isEqualTo($token1 . $token2)
		;
	}
}
