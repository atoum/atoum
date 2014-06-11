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
		$this
			->testedClass
				->isSubClassOf('mageekguy\atoum\php\tokenizer\iterator')
		;
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->array($this->testedInstance->getArguments())->isEmpty()
		;
	}

	public function testReset()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->reset())->isTestedInstance
				->array($this->testedInstance->getArguments())->isEmpty()
			->if($this->testedInstance->appendArgument(new iterators\phpArgument()))
			->then
				->array($this->testedInstance->getArguments())->isNotEmpty()
				->object($this->testedInstance->reset())->isTestedInstance
				->array($this->testedInstance->getArguments())->isEmpty()
		;
	}

	public function testAppendArgument()
	{
		$this
			->if(
				$this->newTestedInstance,
				$argumentIterator = new iterators\phpArgument(),
				$argumentIterator
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
			)
			->then
				->object($this->testedInstance->appendArgument($argumentIterator))->isTestedInstance
				->array($this->testedInstance->getArguments())->isEqualTo(array($argumentIterator))
				->castToString($this->testedInstance)->isEqualTo($token1 . $token2)
		;
	}

	public function testGetName()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getName())->isNull()
			->if($this->testedInstance->append(new tokenizer\token(T_FUNCTION)))
			->then
				->variable($this->testedInstance->getName())->isNull()
			->if($this->testedInstance->append(new tokenizer\token(T_WHITESPACE)))
			->then
				->variable($this->testedInstance->getName())->isNull()
			->if($this->testedInstance->append(new tokenizer\token(T_STRING, $name = uniqid())))
			->then
				->string($this->testedInstance->getName())->isEqualTo($name)
			->if(
				$this->testedInstance->append(new tokenizer\token(T_FUNCTION)),
				$this->testedInstance->append(new tokenizer\token(T_STRING, uniqid()))
			)
			->then
				->string($this->testedInstance->getName())->isEqualTo($name)
		;
	}
}
