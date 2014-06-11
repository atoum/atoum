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
		$this
			->testedClass
				->implements('IteratorAggregate')
		;
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($iterator = $this->testedInstance->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->sizeOf($iterator)->isZero()
		;
	}

	public function testResetIterator()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->resetIterator())->isTestedInstance
				->sizeOf($this->testedInstance->getIterator())->isZero()
				->sizeOf($this->testedInstance->tokenize(uniqid())->getIterator())->isEqualTo(1)
				->object($this->testedInstance->resetIterator())->isTestedInstance
				->sizeOf($this->testedInstance->getIterator())->isZero()
		;

	}

	public function testTokenize()
	{
		$this
			->given($this->newTestedInstance)
			->assert('Tokenizing empty string')
				->object($this->testedInstance->tokenize(''))->isTestedInstance
				->sizeOf($this->testedInstance->getIterator())->isZero()
			->assert('Tokenizing a string which is not PHP code')
				->object($this->testedInstance->tokenize($value = uniqid()))->isTestedInstance
				->castToString($this->testedInstance->getIterator())->isEqualTo($value)
			->assert('Tokenizing open and close PHP tags')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php ?><?php ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php ?>foo<?php ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
			->assert('Tokenizing namespace')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php namespace foo; ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getNamespace(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
					->toString
						->isEqualTo('namespace foo')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php namespace foo ; ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getNamespace(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
					->toString
						->isEqualTo('namespace foo ')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php namespace foo?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getNamespace(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
					->toString
						->isEqualTo('namespace foo')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php namespace foo ?>'))->isTestedInstance
				->castToString($this->testedInstance->getIterator())->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getNamespace(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
					->toString
						->isEqualTo('namespace foo ')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php namespace foo; namespace bar; ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getNamespace(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
					->toString
						->isEqualTo('namespace foo')
				->object($this->testedInstance->getIterator()->getNamespace(1))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
					->toString
						->isEqualTo('namespace bar')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php namespace foo?><?php namespace bar?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getNamespace(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
					->toString
						->isEqualTo('namespace foo')
				->object($this->testedInstance->getIterator()->getNamespace(1))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
					->toString
						->isEqualTo('namespace bar')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php namespace foo ?><?php namespace bar ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getNamespace(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
					->toString
						->isEqualTo('namespace foo ')
				->object($this->testedInstance->getIterator()->getNamespace(1))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
					->toString
						->isEqualTo('namespace bar ')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php namespace foo {} ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getNamespace(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
					->toString
						->isEqualTo('namespace foo {}')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php namespace foo {} namespace bar {} ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getNamespace(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
					->toString
						->isEqualTo('namespace foo {}')
				->object($this->testedInstance->getIterator()->getNamespace(1))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
					->toString
						->isEqualTo('namespace bar {}')
			->assert('Tokenizing constant definition in script')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php const foo = \'foo\'; ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getConstant(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpConstant')
					->toString
						->isEqualTo('const foo = \'foo\'')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php const foo = \'foo\'?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getConstant(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpConstant')
					->toString
						->isEqualTo('const foo = \'foo\'')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php const foo = \'foo\''))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getConstant(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpConstant')
					->toString
						->isEqualTo('const foo = \'foo\'')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php const foo = \'foo\', bar = \'bar\'; ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getConstant(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpConstant')
					->toString
						->isEqualTo('const foo = \'foo\', bar = \'bar\'')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php const foo = \'foo\'?><?php const bar = \'bar\'; ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getConstant(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpConstant')
					->toString
						->isEqualTo('const foo = \'foo\'')
				->object($this->testedInstance->getIterator()->getConstant(1))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpConstant')
					->toString
						->isEqualTo('const bar = \'bar\'')
			->assert('Tokenizing namespace importation in script')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php use foo\bar; ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getImportation(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
					->toString
						->isEqualTo('use foo\bar')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php use foo\bar?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getImportation(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
					->toString->
						isEqualTo('use foo\bar')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php use foo\bar'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getImportation(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
					->toString
						->isEqualTo('use foo\bar')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php use foo\bar; use bar\foo; ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getImportation(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
					->toString
						->isEqualTo('use foo\bar')
				->object($this->testedInstance->getIterator()->getImportation(1))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
					->toString
						->isEqualTo('use bar\foo')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php use foo\bar, bar\foo; ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getImportation(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
					->toString
						->isEqualTo('use foo\bar, bar\foo')
			->assert('Tokenizing namespace importation with aliasing in script')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php use foo\bar as bar; ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->object($this->testedInstance->getIterator()->getImportation(0))
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpImportation')
					->toString
						->isEqualTo('use foo\bar as bar')
			->assert('Tokenizing function definition in script')
				->object($this->testedInstance->resetIterator()->tokenize($php = '<?php function foo() {} ?>'))->isTestedInstance
				->object($this->testedInstance->getIterator())
					->isInstanceOf('mageekguy\atoum\php\tokenizer\iterators\phpScript')
					->toString
						->isEqualTo($php)
				->castToString($this->testedInstance->getIterator()->getFunction(0))
					->isEqualTo('function foo() {}')
		;
	}
}
