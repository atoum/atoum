<?php

namespace mageekguy\atoum\tests\units\php;

use
	\mageekguy\atoum,
	\mageekguy\atoum\php
;

require_once(__DIR__ . '/../../runner.php');

class tokenizer extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->hasInterface('\IteratorAggregate')
		;
	}

	public function test__construct()
	{
		$tokenizer = new php\tokenizer();

		$this->assert
			->object($iterator = $tokenizer->getIterator())->isInstanceOf('\mageekguy\atoum\php\tokenizer\iterator')
			->sizeOf($iterator)->isZero()
		;
	}

	public function testResetIterator()
	{
		$tokenizer = new php\tokenizer();

		$this->assert
			->object($tokenizer->resetIterator())->isIdenticalTo($tokenizer)
			->sizeOf($tokenizer->getIterator())->isZero()
			->sizeOf($tokenizer->tokenize(uniqid())->getIterator())->isEqualTo(1)
			->object($tokenizer->resetIterator())->isIdenticalTo($tokenizer)
			->sizeOf($tokenizer->getIterator())->isZero()
		;

	}

	public function testTokenize()
	{
		$tokenizer = new php\tokenizer();

		$this->assert
			->object($tokenizer->tokenize(''))->isIdenticalTo($tokenizer)
			->sizeOf($tokenizer->getIterator())->isZero()
			->object($tokenizer->tokenize($value = uniqid()))->isIdenticalTo($tokenizer)
			->sizeOf($iterator = $tokenizer->getIterator())->isEqualTo(1)
			->object($iterator->current())->isEqualTo(new php\tokenizer\token(T_INLINE_HTML, $value, 1))
			->object($tokenizer->resetIterator()->tokenize('<?php ?>'))->isIdenticalTo($tokenizer)
			->sizeOf($iterator = $tokenizer->getIterator())->isEqualTo(2)
			->object($iterator->current())->isEqualTo(new php\tokenizer\token(T_OPEN_TAG, '<?php ', 1))
			->object($iterator->next()->current())->isEqualTo(new php\tokenizer\token(T_CLOSE_TAG, '?>', 1))
		;
	}
}

?>
