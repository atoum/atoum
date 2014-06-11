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
				->array($this->testedInstance->getConstants())->isEmpty()
				->array($this->testedInstance->getMethods())->isEmpty()
		;
	}

	public function testAppendConstant()
	{
		$this
			->if(
				$this->newTestedInstance,
				$constantIterator = new iterators\phpConstant(),
				$constantIterator
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
			)
			->then
				->object($this->testedInstance->appendConstant($constantIterator))->isTestedInstance
				->array($this->testedInstance->getConstants())->isEqualTo(array($constantIterator))
				->castToString($this->testedInstance)->isEqualTo($token1 . $token2)
		;
	}

	public function testAppendMethod()
	{
		$this
			->if(
				$this->newTestedInstance,
				$methodIterator = new iterators\phpMethod(),
				$methodIterator
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
			)
			->then
				->object($this->testedInstance->appendMethod($methodIterator))->isTestedInstance
				->array($this->testedInstance->getMethods())->isEqualTo(array($methodIterator))
				->castToString($this->testedInstance)->isEqualTo($token1 . $token2)
		;
	}

	public function testAppendProperty()
	{
		$this
			->if(
				$this->newTestedInstance,
				$propertyIterator = new iterators\phpProperty(),
				$propertyIterator
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
			)
			->then
				->object($this->testedInstance->appendProperty($propertyIterator))->isTestedInstance
				->array($this->testedInstance->getProperties())->isEqualTo(array($propertyIterator))
				->castToString($this->testedInstance)->isEqualTo($token1 . $token2)
		;
	}
}
