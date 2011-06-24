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
			->castToString($tokenizer->getIterator())->isEqualTo($value)
		;

		$this->startCase('Tokenizing open and close PHP tags');

		$tokenizer = new php\tokenizer();

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
		;

		$this->startCase('Tokenizing a single namespace without contents');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo; ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getNamespace(0))->isEqualTo('namespace foo')
		;

		$this->startCase('Tokenizing several namespace without contents');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php namespace foo; namespace bar; ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getNamespace(0))->isEqualTo('namespace foo')
			->castToString($tokenizer->getIterator()->getNamespace(1))->isEqualTo('namespace bar')
		;

		$this->startCase('Tokenizing a single class');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo {} ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo {}')
		;

		$this->startCase('Tokenizing a single class with a single constant');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { const bar = \'bar\'; } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { const bar = \'bar\'; }')
			->castToString($tokenizer->getIterator()->getClass(0)->getConstant(0))->isEqualTo('const bar = \'bar\'')
		;


		$this->startCase('Tokenizing a single abstract class');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php abstract class foo {} ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('abstract class foo {}')
		;

		$this->startCase('Tokenizing a single final class');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php final class foo {} ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('final class foo {}')
		;

		$this->startCase('Tokenizing several classes');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo {} class bar {} ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo {}')
			->castToString($tokenizer->getIterator()->getClass(1))->isEqualTo('class bar {}')
		;

		$this->startCase('Tokenizing single class with a single public property');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { public $bar; } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { public $bar; }')
			->castToString($tokenizer->getIterator()->getClass(0)->getProperty(0))->isEqualTo('public $bar')
		;

		$this->startCase('Tokenizing single class with a single protected property');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { protected $bar; } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { protected $bar; }')
			->castToString($tokenizer->getIterator()->getClass(0)->getProperty(0))->isEqualTo('protected $bar')
		;

		$this->startCase('Tokenizing single class with a single private property');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { private $bar; } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { private $bar; }')
			->castToString($tokenizer->getIterator()->getClass(0)->getProperty(0))->isEqualTo('private $bar')
		;

		$this->startCase('Tokenizing single class with several public properties');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { public $bar, $foo; } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { public $bar, $foo; }')
			->castToString($tokenizer->getIterator()->getClass(0)->getProperty(0))->isEqualTo('public $bar')
			->castToString($tokenizer->getIterator()->getClass(0)->getProperty(1))->isEqualTo('$foo')
		;

		$this->startCase('Tokenizing single class with several distinct public properties');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { public $bar; public $foo; } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { public $bar; public $foo; }')
			->castToString($tokenizer->getIterator()->getClass(0)->getProperty(0))->isEqualTo('public $bar')
			->castToString($tokenizer->getIterator()->getClass(0)->getProperty(1))->isEqualTo('public $foo')
		;

		$this->startCase('Tokenizing single class with single implicit public method');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { function bar() {} } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { function bar() {} }')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0))->isEqualTo('function bar() {}')
		;

		$this->startCase('Tokenizing single class with single explicit public method');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { public function bar() {} } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { public function bar() {} }')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0))->isEqualTo('public function bar() {}')
		;

		$this->startCase('Tokenizing single class with single protected method');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { protected function bar() {} } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { protected function bar() {} }')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0))->isEqualTo('protected function bar() {}')
		;

		$this->startCase('Tokenizing single class with single private method');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { private function bar() {} } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { private function bar() {} }')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0))->isEqualTo('private function bar() {}')
		;

		$this->startCase('Tokenizing single class with single explicit public method and one argument');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { public function bar($foo) {} } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { public function bar($foo) {} }')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0))->isEqualTo('public function bar($foo) {}')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0)->getArgument(0))->isEqualTo('$foo')
		;

		$this->startCase('Tokenizing single class with single explicit public method and one argument with default value');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { public function bar($foo = \'foo\') {} } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { public function bar($foo = \'foo\') {} }')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0))->isEqualTo('public function bar($foo = \'foo\') {}')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0)->getArgument(0))->isEqualTo('$foo = \'foo\'')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0)->getArgument(0)->getDefaultValue())->isEqualTo('\'foo\'')
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { public function bar($foo=\'foo\') {} } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { public function bar($foo=\'foo\') {} }')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0))->isEqualTo('public function bar($foo=\'foo\') {}')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0)->getArgument(0))->isEqualTo('$foo=\'foo\'')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0)->getArgument(0)->getDefaultValue())->isEqualTo('\'foo\'')
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { public function bar($foo= /* Comment */ \'foo\') {} } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { public function bar($foo= /* Comment */ \'foo\') {} }')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0))->isEqualTo('public function bar($foo= /* Comment */ \'foo\') {}')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0)->getArgument(0))->isEqualTo('$foo= /* Comment */ \'foo\'')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0)->getArgument(0)->getDefaultValue())->isEqualTo('\'foo\'')
		;

		$this->startCase('Tokenizing single class with single explicit public method and one argument with array as default value');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { public function bar($foo = array()) {} } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { public function bar($foo = array()) {} }')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0))->isEqualTo('public function bar($foo = array()) {}')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0)->getArgument(0))->isEqualTo('$foo = array()')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0)->getArgument(0)->getDefaultValue())->isEqualTo('array()')
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { public function bar($foo = array(array())) {} } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { public function bar($foo = array(array())) {} }')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0))->isEqualTo('public function bar($foo = array(array())) {}')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0)->getArgument(0))->isEqualTo('$foo = array(array())')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0)->getArgument(0)->getDefaultValue())->isEqualTo('array(array())')
		;

		$this->startCase('Tokenizing single class with single explicit public method and several arguments');

		$this->assert
			->object($tokenizer->resetIterator()->tokenize($php = '<?php class foo { public function bar($foo, $bar) {} } ?>'))->isIdenticalTo($tokenizer)
			->castToString($tokenizer->getIterator())->isEqualTo($php)
			->castToString($tokenizer->getIterator()->getClass(0))->isEqualTo('class foo { public function bar($foo, $bar) {} }')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0))->isEqualTo('public function bar($foo, $bar) {}')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0)->getArgument(0))->isEqualTo('$foo')
			->castToString($tokenizer->getIterator()->getClass(0)->getMethod(0)->getArgument(1))->isEqualTo('$bar')
		;
	}
}

?>
