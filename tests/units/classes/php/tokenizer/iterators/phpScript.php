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
		$this
			->testedClass
				->isSubClassOf('mageekguy\atoum\php\tokenizer\iterators\phpNamespace')
		;
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->array($this->testedInstance->getConstants())->isEmpty()
				->array($this->testedInstance->getClasses())->isEmpty()
				->array($this->testedInstance->getNamespaces())->isEmpty()
				->array($this->testedInstance->getImportations())->isEmpty()
		;
	}

	public function testReset()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->reset())->isTestedInstance
				->array($this->testedInstance->getConstants())->isEmpty()
				->array($this->testedInstance->getClasses())->isEmpty()
				->array($this->testedInstance->getNamespaces())->isEmpty()
			->if(
				$this->testedInstance->appendConstant(new iterators\phpConstant()),
				$this->testedInstance->appendClass(new iterators\phpClass()),
				$this->testedInstance->appendNamespace(new iterators\phpNamespace())
			)
			->then
				->array($this->testedInstance->getConstants())->isNotEmpty()
				->array($this->testedInstance->getClasses())->isNotEmpty()
				->array($this->testedInstance->getNamespaces())->isNotEmpty()
				->object($this->testedInstance->reset())->isTestedInstance
				->array($this->testedInstance->getConstants())->isEmpty()
				->array($this->testedInstance->getClasses())->isEmpty()
				->array($this->testedInstance->getNamespaces())->isEmpty()
		;
	}

	public function testAppendConstant()
	{
		$this
			->if(
				$this->newTestedInstance,
				$constantIterator = new iterators\phpConstant(),
				$constantIterator
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
			)
			->then
				->object($this->testedInstance->appendConstant($constantIterator))->isTestedInstance
				->array($this->testedInstance->getConstants())->isEqualTo(array($constantIterator))
				->castToString($this->testedInstance)->isEqualTo($token1 . $token2)
		;
	}

	public function testGetConstants()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->array($this->testedInstance->getConstants())->isEmpty()
			->if($this->testedInstance->appendConstant($constantIterator = new iterators\phpConstant()))
			->then
				->array($this->testedInstance->getConstants())->isEqualTo(array($constantIterator))
			->if($this->testedInstance->appendConstant($otherConstantIterator = new iterators\phpConstant()))
			->then
				->array($this->testedInstance->getConstants())->isEqualTo(array($constantIterator, $otherConstantIterator))
		;
	}

	public function testGetConstant()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getConstant(rand(0, PHP_INT_MAX)))->isNull()
			->if($this->testedInstance->appendConstant($constantIterator = new iterators\phpConstant()))
			->then
				->variable($this->testedInstance->getConstant(0))->isIdenticalTo($constantIterator)
				->variable($this->testedInstance->getConstant(rand(1, PHP_INT_MAX)))->isNull()
			->if($this->testedInstance->appendConstant($otherConstantIterator = new iterators\phpConstant()))
			->then
				->variable($this->testedInstance->getConstant(0))->isIdenticalTo($constantIterator)
				->variable($this->testedInstance->getConstant(1))->isIdenticalTo($otherConstantIterator)
				->variable($this->testedInstance->getConstant(rand(2, PHP_INT_MAX)))->isNull()
		;
	}

	public function testAppendClass()
	{
		$this
			->if(
				$this->newTestedInstance,
				$classIterator = new iterators\phpClass(),
				$classIterator
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
			)
			->then
				->object($this->testedInstance->appendClass($classIterator))->isTestedInstance
				->array($this->testedInstance->getClasses())->isEqualTo(array($classIterator))
				->castToString($this->testedInstance)->isEqualTo($token1 . $token2)
		;
	}

	public function testGetClasses()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->array($this->testedInstance->getClasses())->isEmpty()
			->if($this->testedInstance->appendClass($classIterator = new iterators\phpClass()))
			->then
				->array($this->testedInstance->getClasses())->isEqualTo(array($classIterator))
			->if($this->testedInstance->appendClass($otherClassIterator = new iterators\phpClass()))
			->then
				->array($this->testedInstance->getClasses())->isEqualTo(array($classIterator, $otherClassIterator))
		;
	}

	public function testGetClass()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getClass(rand(0, PHP_INT_MAX)))->isNull()
			->if($this->testedInstance->appendClass($classIterator = new iterators\phpClass()))
			->then
				->variable($this->testedInstance->getClass(0))->isIdenticalTo($classIterator)
				->variable($this->testedInstance->getClass(rand(1, PHP_INT_MAX)))->isNull()
			->if($this->testedInstance->appendClass($otherClassIterator = new iterators\phpClass()))
			->then
				->variable($this->testedInstance->getClass(0))->isIdenticalTo($classIterator)
				->variable($this->testedInstance->getClass(1))->isIdenticalTo($otherClassIterator)
				->variable($this->testedInstance->getClass(rand(2, PHP_INT_MAX)))->isNull()
		;
	}

	public function testAppendNamespace()
	{
		$this
			->if(
				$this->newTestedInstance,
				$namespaceIterator = new iterators\phpNamespace(),
				$namespaceIterator
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
			)
			->then
				->object($this->testedInstance->appendNamespace($namespaceIterator))->isTestedInstance
				->array($this->testedInstance->getNamespaces())->isEqualTo(array($namespaceIterator))
				->castToString($this->testedInstance)->isEqualTo($token1 . $token2)
		;
	}

	public function testGetNamespaces()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->array($this->testedInstance->getNamespaces())->isEmpty()
			->if($this->testedInstance->appendNamespace($namespaceIterator = new iterators\phpNamespace()))
			->then
				->array($this->testedInstance->getNamespaces())->isEqualTo(array($namespaceIterator))
			->if($this->testedInstance->appendNamespace($otherNamespaceIterator = new iterators\phpNamespace()))
			->then
				->array($this->testedInstance->getNamespaces())->isEqualTo(array($namespaceIterator, $otherNamespaceIterator))
		;
	}

	public function testGetNamespace()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getNamespace(rand(0, PHP_INT_MAX)))->isNull()
			->if($this->testedInstance->appendNamespace($namespaceIterator = new iterators\phpNamespace()))
			->then
				->variable($this->testedInstance->getNamespace(0))->isIdenticalTo($namespaceIterator)
				->variable($this->testedInstance->getNamespace(rand(1, PHP_INT_MAX)))->isNull()
			->if($this->testedInstance->appendNamespace($otherNamespaceIterator = new iterators\phpNamespace()))
			->then
				->variable($this->testedInstance->getNamespace(0))->isIdenticalTo($namespaceIterator)
				->variable($this->testedInstance->getNamespace(1))->isIdenticalTo($otherNamespaceIterator)
				->variable($this->testedInstance->getNamespace(rand(2, PHP_INT_MAX)))->isNull()
		;
	}

	public function testAppendImportation()
	{
		$this
			->if(
				$this->newTestedInstance,
				$importationIterator = new iterators\phpImportation(),
				$importationIterator
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
			)
			->then
				->object($this->testedInstance->appendImportation($importationIterator))->isTestedInstance
				->array($this->testedInstance->getImportations())->isEqualTo(array($importationIterator))
				->castToString($this->testedInstance)->isEqualTo($token1 . $token2)
		;
	}

	public function testGetImportations()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->array($this->testedInstance->getImportations())->isEmpty()
			->if($this->testedInstance->appendImportation($importationIterator = new iterators\phpImportation()))
			->then
				->array($this->testedInstance->getImportations())->isEqualTo(array($importationIterator))
			->if($this->testedInstance->appendImportation($otherImportationIterator = new iterators\phpImportation()))
			->then
				->array($this->testedInstance->getImportations())->isEqualTo(array($importationIterator, $otherImportationIterator))
		;
	}

	public function testGetImportation()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getImportation(rand(0, PHP_INT_MAX)))->isNull()
			->if($this->testedInstance->appendImportation($importationIterator = new iterators\phpImportation()))
			->then
				->variable($this->testedInstance->getImportation(0))->isIdenticalTo($importationIterator)
				->variable($this->testedInstance->getImportation(rand(1, PHP_INT_MAX)))->isNull()
			->if($this->testedInstance->appendImportation($otherImportationIterator = new iterators\phpImportation()))
			->then
				->variable($this->testedInstance->getImportation(0))->isIdenticalTo($importationIterator)
				->variable($this->testedInstance->getImportation(1))->isIdenticalTo($otherImportationIterator)
				->variable($this->testedInstance->getImportation(rand(2, PHP_INT_MAX)))->isNull()
		;
	}

	public function testAppendFunction()
	{
		$this
			->if(
				$this->newTestedInstance,
				$functionIterator = new iterators\phpFunction(),
				$functionIterator
					->append($token1 = new tokenizer\token(uniqid()))
					->append($token2 = new tokenizer\token(uniqid()))
			)
			->then
				->object($this->testedInstance->appendFunction($functionIterator))->isTestedInstance
				->array($this->testedInstance->getFunctions())->isEqualTo(array($functionIterator))
				->castToString($this->testedInstance)->isEqualTo($token1 . $token2)
		;
	}

	public function testGetFunctions()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->array($this->testedInstance->getFunctions())->isEmpty()
			->if($this->testedInstance->appendFunction($functionIterator = new iterators\phpFunction()))
			->then
				->array($this->testedInstance->getFunctions())->isEqualTo(array($functionIterator))
			->if($this->testedInstance->appendFunction($otherFunctionIterator = new iterators\phpFunction()))
			->then
				->array($this->testedInstance->getFunctions())->isEqualTo(array($functionIterator, $otherFunctionIterator))
		;
	}

	public function testGetFunction()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getFunction(rand(0, PHP_INT_MAX)))->isNull()
			->if($this->testedInstance->appendFunction($functionIterator = new iterators\phpFunction()))
			->then
				->variable($this->testedInstance->getFunction(0))->isIdenticalTo($functionIterator)
				->variable($this->testedInstance->getFunction(rand(1, PHP_INT_MAX)))->isNull()
			->if($this->testedInstance->appendFunction($otherFunctionIterator = new iterators\phpFunction()))
			->then
				->variable($this->testedInstance->getFunction(0))->isIdenticalTo($functionIterator)
				->variable($this->testedInstance->getFunction(1))->isIdenticalTo($otherFunctionIterator)
				->variable($this->testedInstance->getFunction(rand(2, PHP_INT_MAX)))->isNull()
		;
	}
}
