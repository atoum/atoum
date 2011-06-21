<?php

namespace mageekguy\atoum\tests\units\php\tokenizer\iterators;

use
	\mageekguy\atoum,
	\mageekguy\atoum\php\tokenizer,
	\mageekguy\atoum\php\tokenizer\iterators
;

require_once(__DIR__ . '/../../../runner.php');

class phpScript extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubClassOf('\mageekguy\atoum\php\tokenizer\iterator')
		;
	}

	public function test__construct()
	{
		$iterator = new iterators\phpScript();

		$this->assert
			->array($iterator->getClasses())->isEmpty()
			->array($iterator->getNamespaces())->isEmpty()
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

	public function testAppendNamespace()
	{
		$iterator = new iterators\phpScript();

		$namespaceIterator = new iterators\phpNamespace();
		$namespaceIterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->object($iterator->appendNamespace($namespaceIterator))->isIdenticalTo($iterator)
			->array($iterator->getNamespaces())->isEqualTo(array($namespaceIterator))
			->castToString($iterator)->isEqualTo($token1 . $token2)
		;
	}
}

?>
