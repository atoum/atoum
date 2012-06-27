<?php

namespace mageekguy\atoum\tests\units\php;

use
	mageekguy\atoum,
	mageekguy\atoum\php
;

require_once __DIR__ . '/../../runner.php';

class tokenizer extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->hasInterface('IteratorAggregate')
		;
	}

	public function test__construct()
	{
		$tokenizer = new php\tokenizer();

		$this->assert
			->object($iterator = $tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
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
			->castToString($tokenizer->getIterator())->isEqualTo($value)
		;

		$this->startCase('Tokenizing open and close PHP tags');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php ?><?php ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php ?>foo<?php ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
		;

		$this->startCase('Tokenizing namespace');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo; ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getNamespace(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
				->toString
					->isEqualTo('namespace foo')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo ; ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getNamespace(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
				->toString
					->isEqualTo('namespace foo ')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getNamespace(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
				->toString
					->isEqualTo('namespace foo')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->object($tokenizer->getIterator()->getNamespace(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
				->toString
					->isEqualTo('namespace foo ')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo; namespace bar; ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getNamespace(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
				->toString
					->isEqualTo('namespace foo')
			->object($tokenizer->getIterator()->getNamespace(1))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
				->toString
					->isEqualTo('namespace bar')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo?><?php namespace bar?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getNamespace(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
				->toString
					->isEqualTo('namespace foo')
			->object($tokenizer->getIterator()->getNamespace(1))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
				->toString
					->isEqualTo('namespace bar')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo ?><?php namespace bar ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getNamespace(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
				->toString
					->isEqualTo('namespace foo ')
			->object($tokenizer->getIterator()->getNamespace(1))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
				->toString
					->isEqualTo('namespace bar ')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo {} ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getNamespace(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
				->toString
					->isEqualTo('namespace foo {}')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo {} namespace bar {} ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getNamespace(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
				->toString
					->isEqualTo('namespace foo {}')
			->object($tokenizer->getIterator()->getNamespace(1))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
				->toString
					->isEqualTo('namespace bar {}')
		;

		$this->startCase('Tokenizing constant definition in script');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php const foo = \'foo\'; ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getConstant(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpConstant')
				->toString
					->isEqualTo('const foo = \'foo\'')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php const foo = \'foo\'?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getConstant(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpConstant')
				->toString
					->isEqualTo('const foo = \'foo\'')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php const foo = \'foo\''))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getConstant(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpConstant')
				->toString
					->isEqualTo('const foo = \'foo\'')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php const foo = \'foo\', bar = \'bar\'; ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getConstant(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpConstant')
				->toString
					->isEqualTo('const foo = \'foo\', bar = \'bar\'')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php const foo = \'foo\'?><?php const bar = \'bar\'; ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getConstant(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpConstant')
				->toString
					->isEqualTo('const foo = \'foo\'')
			->object($tokenizer->getIterator()->getConstant(1))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpConstant')
				->toString
					->isEqualTo('const bar = \'bar\'')
		;

		$this->startCase('Tokenizing namespace importation in script');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php use foo\bar; ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getImportation(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
				->toString
					->isEqualTo('use foo\bar')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php use foo\bar?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getImportation(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
				->toString->
					isEqualTo('use foo\bar')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php use foo\bar'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getImportation(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
				->toString
					->isEqualTo('use foo\bar')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php use foo\bar; use bar\foo; ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getImportation(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
				->toString
					->isEqualTo('use foo\bar')
			->object($tokenizer->getIterator()->getImportation(1))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
				->toString
					->isEqualTo('use bar\foo')
		;

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php use foo\bar, bar\foo; ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getImportation(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
				->toString
					->isEqualTo('use foo\bar, bar\foo')
		;

		$this->startCase('Tokenizing namespace importation with aliasing in script');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php use foo\bar as bar; ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->object($tokenizer->getIterator()->getImportation(0))
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
				->toString
					->isEqualTo('use foo\bar as bar')
		;

		$this->startCase('Tokenizing function definition in script');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php function foo() {} ?>'))->isIdenticalTo($tokenizer)
			->object($tokenizer->getIterator())
				->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
				->toString
					->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getFunction(0))
				->isEqualTo('function foo() {}')
		;
	}
}
