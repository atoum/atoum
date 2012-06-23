<?php

namespace mageekguy\atoum\tests\units\php\tokenizer\iterators;

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizer\iterators
;

require_once __DIR__ . '/../../../../runner.php';

class phpFunction extends atoum\test
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
		$iterator = new iterators\phpFunction();

		$this->assert
			->array($iterator->getArguments())->isEmpty()
		;
	}

	public function testReset()
	{
		$iterator = new iterators\phpFunction();

		$this->assert
			->object($iterator->reset())->isIdenticalTo($iterator)
			->array($iterator->getArguments())->isEmpty()
		;

		$iterator->appendArgument(new iterators\phpArgument());

		$this->assert
			->array($iterator->getArguments())->isNotEmpty()
			->object($iterator->reset())->isIdenticalTo($iterator)
			->array($iterator->getArguments())->isEmpty()
		;
	}

	public function testAppendArgument()
	{
		$iterator = new iterators\phpFunction();

		$argumentIterator = new iterators\phpArgument();
		$argumentIterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->object($iterator->appendArgument($argumentIterator))->isIdenticalTo($iterator)
			->array($iterator->getArguments())->isEqualTo(array($argumentIterator))
			->castToString($iterator)->isEqualTo($token1 . $token2)
		;
	}

	public function testGetName()
	{
		$iterator = new iterators\phpFunction();

		$this->assert
			->variable($iterator->getName())->isNull()
		;

		$iterator->append(new tokenizer\token(T_FUNCTION));

		$this->assert
			->variable($iterator->getName())->isNull()
		;

		$iterator->append(new tokenizer\token(T_WHITESPACE));

		$this->assert
			->variable($iterator->getName())->isNull()
		;

		$iterator->append(new tokenizer\token(T_STRING, $name = uniqid()));

		$this->assert
			->string($iterator->getName())->isEqualTo($name)
		;

		$iterator->append(new tokenizer\token(T_FUNCTION));
		$iterator->append(new tokenizer\token(T_STRING, uniqid()));

		$this->assert
			->string($iterator->getName())->isEqualTo($name)
		;
	}
}
