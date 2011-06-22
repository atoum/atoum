<?php

namespace mageekguy\atoum\tests\units\php\tokenizer\iterators;

use
	\mageekguy\atoum,
	\mageekguy\atoum\php\tokenizer,
	\mageekguy\atoum\php\tokenizer\iterators
;

require_once(__DIR__ . '/../../../../runner.php');

class phpNamespace extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubClassOf('\mageekguy\atoum\php\tokenizer\iterator')
		;
	}

	public function testAppendClass()
	{
		$iterator = new iterators\phpScript();

		$classIterator = new iterators\phpClass();
		$classIterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->object($iterator->appendClass($classIterator))->isIdenticalTo($iterator)
			->array($iterator->getClasses())->isEqualTo(array($classIterator))
			->castToString($iterator)->isEqualTo($token1 . $token2)
		;
	}
}

?>
