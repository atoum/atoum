<?php

namespace mageekguy\atoum\tests\units\php\tokenizer\iterators;

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizer\iterators
;

require_once __DIR__ . '/../../../../runner.php';

class phpScript extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubClassOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
		;
	}

	public function test__construct()
	{
		$iterator = new iterators\phpScript();

		$this->assert
			->array($iterator->getConstants())->isEmpty()
			->array($iterator->getClasses())->isEmpty()
			->array($iterator->getNamespaces())->isEmpty()
			->array($iterator->getImportations())->isEmpty()
		;
	}

	public function testReset()
	{
		$iterator = new iterators\phpScript();

		$this->assert
			->object($iterator->reset())->isIdenticalTo($iterator)
			->array($iterator->getConstants())->isEmpty()
			->array($iterator->getClasses())->isEmpty()
			->array($iterator->getNamespaces())->isEmpty()
		;

		$iterator->appendConstant(new iterators\phpConstant());
		$iterator->appendClass(new iterators\phpClass());
		$iterator->appendNamespace(new iterators\phpNamespace());

		$this->assert
			->array($iterator->getConstants())->isNotEmpty()
			->array($iterator->getClasses())->isNotEmpty()
			->array($iterator->getNamespaces())->isNotEmpty()
			->object($iterator->reset())->isIdenticalTo($iterator)
			->array($iterator->getConstants())->isEmpty()
			->array($iterator->getClasses())->isEmpty()
			->array($iterator->getNamespaces())->isEmpty()
		;
	}

	public function testAppendConstant()
	{
		$iterator = new iterators\phpScript();

		$constantIterator = new iterators\phpConstant();
		$constantIterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->object($iterator->appendConstant($constantIterator))->isIdenticalTo($iterator)
			->array($iterator->getConstants())->isEqualTo(array($constantIterator))
			->castToString($iterator)->isEqualTo($token1 . $token2)
		;
	}

	public function testGetConstants()
	{
		$iterator = new iterators\phpScript();

		$this->assert
			->array($iterator->getConstants())->isEmpty()
		;

		$iterator->appendConstant($constantIterator = new iterators\phpConstant());

		$this->assert
			->array($iterator->getConstants())->isEqualTo(array($constantIterator))
		;

		$iterator->appendConstant($otherConstantIterator = new iterators\phpConstant());

		$this->assert
			->array($iterator->getConstants())->isEqualTo(array($constantIterator, $otherConstantIterator))
		;
	}

	public function testGetConstant()
	{
		$iterator = new iterators\phpScript();

		$this->assert
			->variable($iterator->getConstant(rand(0, PHP_INT_MAX)))->isNull()
		;

		$iterator->appendConstant($constantIterator = new iterators\phpConstant());

		$this->assert
			->variable($iterator->getConstant(0))->isIdenticalTo($constantIterator)
			->variable($iterator->getConstant(rand(1, PHP_INT_MAX)))->isNull()
		;

		$iterator->appendConstant($otherConstantIterator = new iterators\phpConstant());

		$this->assert
			->variable($iterator->getConstant(0))->isIdenticalTo($constantIterator)
			->variable($iterator->getConstant(1))->isIdenticalTo($otherConstantIterator)
			->variable($iterator->getConstant(rand(2, PHP_INT_MAX)))->isNull()
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

	public function testGetClasses()
	{
		$iterator = new iterators\phpScript();

		$this->assert
			->array($iterator->getClasses())->isEmpty()
		;

		$iterator->appendClass($classIterator = new iterators\phpClass());

		$this->assert
			->array($iterator->getClasses())->isEqualTo(array($classIterator))
		;

		$iterator->appendClass($otherClassIterator = new iterators\phpClass());

		$this->assert
			->array($iterator->getClasses())->isEqualTo(array($classIterator, $otherClassIterator))
		;
	}

	public function testGetClass()
	{
		$iterator = new iterators\phpScript();

		$this->assert
			->variable($iterator->getClass(rand(0, PHP_INT_MAX)))->isNull()
		;

		$iterator->appendClass($classIterator = new iterators\phpClass());

		$this->assert
			->variable($iterator->getClass(0))->isIdenticalTo($classIterator)
			->variable($iterator->getClass(rand(1, PHP_INT_MAX)))->isNull()
		;

		$iterator->appendClass($otherClassIterator = new iterators\phpClass());

		$this->assert
			->variable($iterator->getClass(0))->isIdenticalTo($classIterator)
			->variable($iterator->getClass(1))->isIdenticalTo($otherClassIterator)
			->variable($iterator->getClass(rand(2, PHP_INT_MAX)))->isNull()
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

	public function testGetNamespaces()
	{
		$iterator = new iterators\phpScript();

		$this->assert
			->array($iterator->getNamespaces())->isEmpty()
		;

		$iterator->appendNamespace($namespaceIterator = new iterators\phpNamespace());

		$this->assert
			->array($iterator->getNamespaces())->isEqualTo(array($namespaceIterator))
		;

		$iterator->appendNamespace($otherNamespaceIterator = new iterators\phpNamespace());

		$this->assert
			->array($iterator->getNamespaces())->isEqualTo(array($namespaceIterator, $otherNamespaceIterator))
		;
	}

	public function testGetNamespace()
	{
		$iterator = new iterators\phpScript();

		$this->assert
			->variable($iterator->getNamespace(rand(0, PHP_INT_MAX)))->isNull()
		;

		$iterator->appendNamespace($namespaceIterator = new iterators\phpNamespace());

		$this->assert
			->variable($iterator->getNamespace(0))->isIdenticalTo($namespaceIterator)
			->variable($iterator->getNamespace(rand(1, PHP_INT_MAX)))->isNull()
		;

		$iterator->appendNamespace($otherNamespaceIterator = new iterators\phpNamespace());

		$this->assert
			->variable($iterator->getNamespace(0))->isIdenticalTo($namespaceIterator)
			->variable($iterator->getNamespace(1))->isIdenticalTo($otherNamespaceIterator)
			->variable($iterator->getNamespace(rand(2, PHP_INT_MAX)))->isNull()
		;
	}

	public function testAppendImportation()
	{
		$iterator = new iterators\phpScript();

		$importationIterator = new iterators\phpImportation();
		$importationIterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->object($iterator->appendImportation($importationIterator))->isIdenticalTo($iterator)
			->array($iterator->getImportations())->isEqualTo(array($importationIterator))
			->castToString($iterator)->isEqualTo($token1 . $token2)
		;
	}

	public function testGetImportations()
	{
		$iterator = new iterators\phpScript();

		$this->assert
			->array($iterator->getImportations())->isEmpty()
		;

		$iterator->appendImportation($importationIterator = new iterators\phpImportation());

		$this->assert
			->array($iterator->getImportations())->isEqualTo(array($importationIterator))
		;

		$iterator->appendImportation($otherImportationIterator = new iterators\phpImportation());

		$this->assert
			->array($iterator->getImportations())->isEqualTo(array($importationIterator, $otherImportationIterator))
		;
	}

	public function testGetImportation()
	{
		$iterator = new iterators\phpScript();

		$this->assert
			->variable($iterator->getImportation(rand(0, PHP_INT_MAX)))->isNull()
		;

		$iterator->appendImportation($importationIterator = new iterators\phpImportation());

		$this->assert
			->variable($iterator->getImportation(0))->isIdenticalTo($importationIterator)
			->variable($iterator->getImportation(rand(1, PHP_INT_MAX)))->isNull()
		;

		$iterator->appendImportation($otherImportationIterator = new iterators\phpImportation());

		$this->assert
			->variable($iterator->getImportation(0))->isIdenticalTo($importationIterator)
			->variable($iterator->getImportation(1))->isIdenticalTo($otherImportationIterator)
			->variable($iterator->getImportation(rand(2, PHP_INT_MAX)))->isNull()
		;
	}

	public function testAppendFunction()
	{
		$iterator = new iterators\phpScript();

		$functionIterator = new iterators\phpFunction();
		$functionIterator
			->append($token1 = new tokenizer\token(uniqid()))
			->append($token2 = new tokenizer\token(uniqid()))
		;

		$this->assert
			->object($iterator->appendFunction($functionIterator))->isIdenticalTo($iterator)
			->array($iterator->getFunctions())->isEqualTo(array($functionIterator))
			->castToString($iterator)->isEqualTo($token1 . $token2)
		;
	}

	public function testGetFunctions()
	{
		$iterator = new iterators\phpScript();

		$this->assert
			->array($iterator->getFunctions())->isEmpty()
		;

		$iterator->appendFunction($functionIterator = new iterators\phpFunction());

		$this->assert
			->array($iterator->getFunctions())->isEqualTo(array($functionIterator))
		;

		$iterator->appendFunction($otherFunctionIterator = new iterators\phpFunction());

		$this->assert
			->array($iterator->getFunctions())->isEqualTo(array($functionIterator, $otherFunctionIterator))
		;
	}

	public function testGetFunction()
	{
		$iterator = new iterators\phpScript();

		$this->assert
			->variable($iterator->getFunction(rand(0, PHP_INT_MAX)))->isNull()
		;

		$iterator->appendFunction($functionIterator = new iterators\phpFunction());

		$this->assert
			->variable($iterator->getFunction(0))->isIdenticalTo($functionIterator)
			->variable($iterator->getFunction(rand(1, PHP_INT_MAX)))->isNull()
		;

		$iterator->appendFunction($otherFunctionIterator = new iterators\phpFunction());

		$this->assert
			->variable($iterator->getFunction(0))->isIdenticalTo($functionIterator)
			->variable($iterator->getFunction(1))->isIdenticalTo($otherFunctionIterator)
			->variable($iterator->getFunction(rand(2, PHP_INT_MAX)))->isNull()
		;
	}
}
