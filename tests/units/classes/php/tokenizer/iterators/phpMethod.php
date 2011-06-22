<?php

namespace mageekguy\atoum\tests\units\php\tokenizer\iterators;

use
	\mageekguy\atoum,
	\mageekguy\atoum\php\tokenizer,
	\mageekguy\atoum\php\tokenizer\iterators
;

require_once(__DIR__ . '/../../../../runner.php');

class phpMethod extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubClassOf('\mageekguy\atoum\php\tokenizer\iterator')
		;
	}

	public function testAppendMethod()
	{
		$iterator = new iterators\phpMethod();

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
}

?>
