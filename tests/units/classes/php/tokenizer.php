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

		$tokenizer = new php\tokenizer();

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php ?>'))->isIdenticalTo($tokenizer)
			->castToString($iterator = $tokenizer->getIterator())->isEqualTo($php)
			->sizeOf($iterator = $tokenizer->getIterator())->isEqualTo(2)
			->castToString($iterator->current())->isEqualTo('<?php ')
			->object($iterator->current()->getParent())->isIdenticalTo($iterator)
			->castToString($iterator->next()->current())->isEqualTo('?>')
			->object($iterator->current()->getParent())->isIdenticalTo($iterator)
			->boolean($iterator->next()->valid())->isFalse()
		;

		$this->startCase('Tokenizing a single namespace without contents');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo; ?>'))->isIdenticalTo($tokenizer)
			->castToString($iterator = $tokenizer->getIterator())->isEqualTo($php)
			->sizeOf($iterator = $tokenizer->getIterator())->isEqualTo(7)
			->castToString($iterator->current())->isEqualTo('<?php ')
			->object($iterator->current()->getParent())->isIdenticalTo($iterator)
			->castToString($iterator->next()->current())->isEqualTo('namespace')
			->object($iterator->current()->getParent())->isIdenticalTo($iterator->getValue())
			->object($namespaceIterator = $iterator->getValue())->isInstanceOf('\mageekguy\atoum\php\tokenizer\iterator')
			->object($namespaceIterator->getParent())->isIdenticalTo($iterator)
			->boolean($namespaceIterator->valid())->isTrue()
			->castToString($namespaceIterator->current())->isEqualTo('namespace')
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->castToString($namespaceIterator->next()->current())->isEqualTo(' ')
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->castToString($namespaceIterator->next()->current())->isEqualTo('foo')
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->castToString($namespaceIterator->next()->current())->isEqualTo(';')
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->castToString($namespaceIterator->next()->current())->isEqualTo(' ')
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->boolean($namespaceIterator->next()->valid())->isFalse()
			->castToString($iterator->end()->current())->isEqualTo('?>')
			->boolean($iterator->next()->valid())->isFalse()
		;

		$this->startCase('Tokenizing several namespace without contents');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo; namespace bar; ?>'))->isIdenticalTo($tokenizer)
			->castToString($iterator = $tokenizer->getIterator())->isEqualTo($php)
			->sizeOf($iterator)->isEqualTo(12)
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo; class bar {} ?>'))->isIdenticalTo($tokenizer)
			->castToString($iterator = $tokenizer->getIterator())->isEqualTo($php)
			->sizeOf($iterator = $tokenizer->getIterator())->isEqualTo(14)
			->castTostring($iterator->current())->isEqualTo('<?php ')
			->object($iterator->current()->getParent())->isIdenticalTo($iterator)
			->castTostring($iterator->next()->current())->isEqualTo('namespace')
			->object($namespaceIterator = $iterator->getValue())->isInstanceOf('\mageekguy\atoum\php\tokenizer\iterator')
			->boolean($namespaceIterator->valid())->isTrue()
			->object($namespaceIterator->getParent())->isIdenticalTo($iterator)
			->castTostring($namespaceIterator->current())->isEqualTo('namespace')
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->castTostring($namespaceIterator->next()->current())->isEqualTo(' ')
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->castTostring($namespaceIterator->next()->current())->isEqualTo('foo')
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->castTostring($namespaceIterator->next()->current())->isEqualTo(';')
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->castTostring($namespaceIterator->next()->current())->isEqualTo(' ')
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->castTostring($namespaceIterator->next()->current())->isEqualTo('class')
			->object($classIterator = $namespaceIterator->getValue())->isInstanceOf('\mageekguy\atoum\php\tokenizer\iterator')
			->boolean($classIterator->valid())->isTrue()
			->castTostring($classIterator->current())->isEqualTo('class')
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->castTostring($classIterator->next()->current())->isEqualTo(' ')
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->castTostring($classIterator->next()->current())->isEqualTo('bar')
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->castTostring($classIterator->next()->current())->isEqualTo(' ')
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->castTostring($classIterator->next()->current())->isEqualTo('{')
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->castTostring($classIterator->next()->current())->isEqualTo('}')
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->boolean($classIterator->next()->valid())->isFalse()
			->castTostring($namespaceIterator->end()->current())->isEqualTo(' ')
			->object($namespaceIterator->current()->getParent())->isIdenticalTo($namespaceIterator)
			->boolean($namespaceIterator->next()->valid())->isFalse()
			->castTostring($iterator->end()->current())->isEqualTo('?>')
			->object($iterator->current()->getParent())->isIdenticalTo($iterator)
			->boolean($iterator->next()->valid())->isFalse()
		;

		$this->startCase('Tokenizing a single class');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo {} ?>'))->isIdenticalTo($tokenizer)
			->castToString($iterator = $tokenizer->getIterator())->isEqualTo($php)
			->sizeOf($iterator)->isEqualTo(9)
			->castTostring($iterator->current())->isEqualTo('<?php ')
			->object($iterator->current()->getParent())->isIdenticalTo($iterator)
			->castTostring($iterator->next()->current())->isEqualTo('class')
			->object($classIterator = $iterator->getValue())->isInstanceOf('\mageekguy\atoum\php\tokenizer\iterator')
			->boolean($classIterator->valid())->isTrue()
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->castTostring($classIterator->next()->current())->isEqualTo(' ')
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->castTostring($classIterator->next()->current())->isEqualTo('foo')
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->castTostring($classIterator->next()->current())->isEqualTo(' ')
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->castTostring($classIterator->next()->current())->isEqualTo('{')
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->castTostring($classIterator->next()->current())->isEqualTo('}')
			->object($classIterator->current()->getParent())->isIdenticalTo($classIterator)
			->boolean($classIterator->next()->valid())->isFalse()
			->castTostring($iterator->end()->current())->isEqualTo('?>')
			->object($iterator->current()->getParent())->isIdenticalTo($iterator)
			->boolean($iterator->next()->valid())->isFalse()
		;
	}
}

?>
