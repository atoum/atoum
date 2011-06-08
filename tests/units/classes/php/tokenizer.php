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
			->object($iterator->current())->isEqualTo(new php\tokenizer\token(T_INLINE_HTML, $value, 1, $iterator))
			->object($iterator->current()->getParent())->isIdenticalTo($iterator)
		;

		$this->startCase('Tokenizing open and close PHP tags');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php ?>'))->isIdenticalTo($tokenizer)
			->castToString($iterator = $tokenizer->getIterator())->isEqualTo($php)
			->sizeOf($iterator = $tokenizer->getIterator())->isEqualTo(2)
			->object($iterator->current())->isEqualTo(new php\tokenizer\token(T_OPEN_TAG, '<?php ', 1, $iterator))
			->object($iterator->current()->getParent())->isIdenticalTo($iterator)
			->object($iterator->next()->current())->isEqualTo(new php\tokenizer\token(T_CLOSE_TAG, '?>', 1, $iterator))
			->object($iterator->current()->getParent())->isIdenticalTo($iterator)
			->boolean($iterator->next()->valid())->isFalse()
		;

		$this->startCase('Tokenizing a single namespace without contents');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo; ?>'))->isIdenticalTo($tokenizer)
			->castToString($iterator = $tokenizer->getIterator())->isEqualTo($php)
			->sizeOf($iterator = $tokenizer->getIterator())->isEqualTo(7)
			->object($iterator->current())->isEqualTo(new php\tokenizer\token(T_OPEN_TAG, '<?php ', 1, $iterator))
			->object($iterator->current()->getParent())->isIdenticalTo($iterator)
			->object($iterator->next()->current())->isEqualTo(new php\tokenizer\token(T_NAMESPACE, 'namespace', 1, $iterator->getValue()))
			->object($iterator->current()->getParent())->isIdenticalTo($iterator->getValue())
			->object($namespaceIterator = $iterator->getValue())->isInstanceOf('\mageekguy\atoum\php\tokenizer\iterator')
			->object($namespaceIterator->getParent())->isIdenticalTo($iterator)
			->boolean($namespaceIterator->valid())->isTrue()
			->object($namespaceIterator->current())->isEqualTo(new php\tokenizer\token(T_NAMESPACE, 'namespace', 1, $namespaceIterator))
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1, $namespaceIterator))
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_STRING, 'foo', 1, $namespaceIterator))
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(';', null, null, $namespaceIterator))
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1, $namespaceIterator))
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->boolean($namespaceIterator->next()->valid())->isFalse()
			->object($iterator->end()->current())->isEqualTo(new php\tokenizer\token(T_CLOSE_TAG, '?>', 1, $iterator))
			->boolean($iterator->next()->valid())->isFalse()
		;

		$this->startCase('Tokenizing several namespace without contents');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo; namespace bar; ?>'))->isIdenticalTo($tokenizer)
			->castToString($iterator = $tokenizer->getIterator())->isEqualTo($php)
			->sizeOf($iterator)->isEqualTo(12)
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo; class bar {} ?>'))->isIdenticalTo($tokenizer)
			->castToString($iterator = $tokenizer->getIterator())->isEqualTo($php)
			->sizeOf($iterator = $tokenizer->getIterator())->isEqualTo(14)
			->object($iterator->current())->isEqualTo(new php\tokenizer\token(T_OPEN_TAG, '<?php ', 1, $iterator))
			->object($iterator->current()->getParent())->isIdenticalTo($iterator)
			->object($iterator->next()->current())->isEqualTo(new php\tokenizer\token(T_NAMESPACE, 'namespace', 1, $iterator->getValue()))
			->object($namespaceIterator = $iterator->getValue())->isInstanceOf('\mageekguy\atoum\php\tokenizer\iterator')
			->boolean($namespaceIterator->valid())->isTrue()
			->object($namespaceIterator->getParent())->isIdenticalTo($iterator)
			->object($namespaceIterator->current())->isEqualTo(new php\tokenizer\token(T_NAMESPACE, 'namespace', 1, $namespaceIterator))
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1, $namespaceIterator))
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_STRING, 'foo', 1, $namespaceIterator))
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(';', null, null, $namespaceIterator))
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1, $namespaceIterator))
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->object($namespaceIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_CLASS, 'class', 1, $namespaceIterator->getValue()))
			->object($classIterator = $namespaceIterator->getValue())->isInstanceOf('\mageekguy\atoum\php\tokenizer\iterator')
			->boolean($classIterator->valid())->isTrue()
			->object($classIterator->current())->isEqualTo(new php\tokenizer\token(T_CLASS, 'class', 1, $classIterator))
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->object($classIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1, $classIterator))
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->object($classIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_STRING, 'bar', 1, $classIterator))
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->object($classIterator->next()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1, $classIterator))
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->object($classIterator->next()->current())->isEqualTo(new php\tokenizer\token('{', null, null, $classIterator))
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->object($classIterator->next()->current())->isEqualTo(new php\tokenizer\token('}', null, null, $classIterator))
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->boolean($classIterator->next()->valid())->isFalse()
			->object($namespaceIterator->end()->current())->isEqualTo(new php\tokenizer\token(T_WHITESPACE, ' ', 1, $namespaceIterator))
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->boolean($namespaceIterator->next()->valid())->isFalse()
			->object($iterator->end()->current())->isEqualTo(new php\tokenizer\token(T_CLOSE_TAG, '?>', 1, $iterator))
			->object($iterator->current()->getParent())->isIdenticalTo($iterator)
			->boolean($iterator->next()->valid())->isFalse()
		;
	}
}

?>
