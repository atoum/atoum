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

		$this->startCase('Tokenizing empty string');

		$this->assert
			->object($tokenizer->tokenize(''))->isIdenticalTo($tokenizer)
			->sizeOf($tokenizer->getIterator())->isZero()
		;

		$this->startCase('Tokenizing a string which is not PHP code');

		$this->assert
			->object($tokenizer->tokenize($value = uniqid()))->isIdenticalTo($tokenizer)
			->sizeOf($iterator = $tokenizer->getIterator())->isEqualTo(1)
			->object($iterator->current())->isEqualTo(new php\tokenizer\token(T_INLINE_HTML, $value, 1))
		;

		$this->startCase('Tokenizing open and close PHP tags');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php ?>'))->isIdenticalTo($tokenizer)
			->castToString($iterator = $tokenizer->getIterator())->isEqualTo($php)
			->sizeOf($iterator = $tokenizer->getIterator())->isEqualTo(2)
			->object($iterator->current())->isEqualTo(new php\tokenizer\token(T_OPEN_TAG, '<?php ', 1))
			->object($iterator->next()->current())->isEqualTo(new php\tokenizer\token(T_CLOSE_TAG, '?>', 1))
			->boolean($iterator->next()->valid())->isFalse()
		;

		$this->startCase('Tokenizing a single namepspace without contents');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo; ?>'))->isIdenticalTo($tokenizer)
			->castToString($iterator = $tokenizer->getIterator())->isEqualTo($php)
			->sizeOf($iterator = $tokenizer->getIterator())->isEqualTo(7)
			->object($iterator->current())->isEqualTo(new php\tokenizer\token(T_OPEN_TAG, '<?php ', 1))
			->object($iterator->next()->current())->isEqualTo(new php\tokenizer\token(T_NAMESPACE, 'namespace', 1))
			->object($namespaceIterator = $iterator->getValue())->isInstanceOf('\mageekguy\atoum\php\tokenizer\iterator')
			->boolean($namespaceIterator->valid())->isTrue()
			->object($namespaceIterator->current())->isEqualTo(new php\tokenizer\token(T_NAMESPACE, 'namespace', 1))
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1))
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_STRING, 'foo', 1))
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(';', null, null))
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1))
			->boolean($namespaceIterator->next()->valid())->isFalse()
			->object($iterator->end()->current())->isEqualTo(new php\tokenizer\token(T_CLOSE_TAG, '?>', 1))
			->boolean($iterator->next()->valid())->isFalse()
		;

		$this->startCase('Tokenizing several namepspace without contents');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo; namespace bar; ?>'))->isIdenticalTo($tokenizer)
			->castToString($iterator = $tokenizer->getIterator())->isEqualTo($php)
			->sizeOf($iterator)->isEqualTo(12)
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo; class bar {} ?>'))->isIdenticalTo($tokenizer)
			->castToString($iterator = $tokenizer->getIterator())->isEqualTo($php)
			->sizeOf($iterator = $tokenizer->getIterator())->isEqualTo(14)
			->object($iterator->current())->isEqualTo(new php\tokenizer\token(T_OPEN_TAG, '<?php ', 1))
			->object($iterator->next()->current())->isEqualTo(new php\tokenizer\token(T_NAMESPACE, 'namespace', 1))
			->object($namespaceIterator = $iterator->getValue())->isInstanceOf('\mageekguy\atoum\php\tokenizer\iterator')
			->boolean($namespaceIterator->valid())->isTrue()
			->object($namespaceIterator->current())->isEqualTo(new php\tokenizer\token(T_NAMESPACE, 'namespace', 1))
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1))
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_STRING, 'foo', 1))
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(';', null, null))
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1))
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_CLASS, 'class', 1))
			->object($classIterator = $namespaceIterator->getValue())->isInstanceOf('\mageekguy\atoum\php\tokenizer\iterator')
			->boolean($classIterator->valid())->isTrue()
			->object($classIterator->current())->isEqualTo(new php\tokenizer\token(T_CLASS, 'class', 1))
			->object($classIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1))
			->object($classIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_STRING, 'bar', 1))
			->object($classIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1))
			->object($classIterator->next()->current())->isEqualTo(new php\tokenizer\token('{', null, null))
			->object($classIterator->next()->current())->isEqualTo(new php\tokenizer\token('}', null, null))
			->boolean($classIterator->next()->valid())->isFalse()
			->object($namespaceIterator->end()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1))
			->boolean($namespaceIterator->next()->valid())->isFalse()
			->object($iterator->end()->prev()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1))
			->object($iterator->next()->current())->isEqualTo(new php\tokenizer\token(T_CLOSE_TAG, '?>', 1))
			->boolean($iterator->next()->valid())->isFalse()
		;
	}
}

?>
