<?php

namespace mageekguy\atoum\tests\units\php\tokenizer\iterators;

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizer\iterators
;

require_once __DIR__ . '/../../../../runner.php';

class phpNamespace extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubClassOf('mageekguy\atoum\php\tokenizer\iterator')
		;
	}

	public function testAppendClass()
	{
		$phpNamespace = new iterators\phpNamespace();

		$phpClass = new iterators\phpClass();
		$phpClass
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->object($phpNamespace->appendClass($phpClass))->isIdenticalTo($phpNamespace)
			->array($phpNamespace->getClasses())->isEqualTo(array($phpClass))
			->castToString($phpNamespace)->isEqualTo($token1 . $token2)
		;
	}
}
