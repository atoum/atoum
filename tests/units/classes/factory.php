<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require __DIR__ . '/../runner.php';

class factory extends atoum\test
{
	public function test__construct()
	{
		$this->assert
			->if($factory = new atoum\factory())
			->then
				->array($factory->getBuilders())->isEmpty()
				->array($factory->getImportedNamespaces())->isEmpty()
				->variable($factory->getCurrentClass())->isNull()
		;
	}

	public function testBuild()
	{
		$this->assert
			->if($factory = new atoum\factory())
			->then
				->boolean($factory->builderIsSet('arrayIterator'))->isFalse()
				->object($arrayIterator = $factory->build('arrayIterator', array()))->isInstanceOf('arrayIterator')
				->variable($arrayIterator->current())->isNull()
				->boolean($factory->builderIsSet('arrayIterator'))->isFalse()
				->object($arrayIterator = $factory->build('arrayIterator', array(array(1, 2, 3))))->isInstanceOf('arrayIterator')
				->integer($arrayIterator->current())->isEqualTo(1)
				->object($test = $factory->build(__CLASS__))->isInstanceOf(__CLASS__)
			->if($factory->setBuilder('arrayIterator', null))
			->then
				->exception(function() use ($factory) { $factory->build('arrayIterator'); })
					->isInstanceOf('mageekguy\atoum\factory\exception')
					->hasMessage('Unable to build an instance of class \'arrayIterator\' with current builder')
			->if($factory->importNamespace('mageekguy\atoum'))
			->then
				->object($test = $factory->build('atoum\tests\units\factory'))->isInstanceOf(__CLASS__)
				->exception(function() use ($factory) { $factory->build('\atoum\tests\units\factory'); })
					->isInstanceOf('mageekguy\atoum\factory\exception')
					->hasMessage('Unable to build an instance of class \'\atoum\tests\units\factory\' because class does not exist')
			->if($factory->importNamespace('mageekguy\atoum', 'foo'))
			->then
				->object($test = $factory->build('foo\tests\units\factory'))->isInstanceOf(__CLASS__)
		;
	}

	public function testSetBuilder()
	{
		$this->assert
			->if($factory = new atoum\factory())
			->and($arrayIterator = new \arrayIterator())
			->then
				->object($factory->setBuilder('arrayIterator', function() use ($arrayIterator) { return $arrayIterator; }))->isIdenticalTo($factory)
				->boolean($factory->builderIsSet('arrayIterator'))->isTrue()
				->object($factory->build('arrayIterator'))->isIdenticalTo($arrayIterator)
			->if($otherArrayIterator = new \arrayIterator())
				->object($factory->setBuilder('arrayIterator', $otherArrayIterator))->isIdenticalTo($factory)
				->boolean($factory->builderIsSet('arrayIterator'))->isTrue()
				->object($factory->build('arrayIterator'))->isIdenticalTo($otherArrayIterator)
		;
	}

	public function testGetBuilder()
	{
		$this->assert
			->if($factory = new atoum\factory())
			->then
				->variable($factory->getBuilder('arrayIterator'))->isNull()
			->if($factory->setBuilder('arrayIterator', function() {}))
			->then
				->object($factory->getBuilder('arrayIterator'))->isInstanceOf('closure')
		;
	}

	public function testSetCurrentClass()
	{
		$this->assert
			->if($factory = new atoum\factory())
			->then
				->object($factory->setCurrentClass(__CLASS__))->isIdenticalTo($factory)
				->string($factory->getCurrentClass())->isEqualTo(__CLASS__)
				->object($factory->setCurrentClass(($class = uniqid())))->isIdenticalTo($factory)
				->string($factory->getCurrentClass())->isEqualTo($class)
		;
	}

	public function unsetCurrentClass()
	{
		$this->assert
			->if($factory = new atoum\factory())
			->and($factory->setCurrentClass(__CLASS__))
			->then
				->object($factory->unsetCurrentClass())->isIdenticalTo($factory)
				->variable($factory->getCurrentClass())->isNull()
		;
	}

	public function testImportNamespace()
	{
		$this->assert
			->if($factory = new atoum\factory())
			->then
				->object($factory->importNamespace('foo'))->isIdenticalTo($factory)
				->array($factory->getImportedNamespaces())->isEqualTo(array('foo' => 'foo'))
				->object($factory->importNamespace('\foo'))->isIdenticalTo($factory)
				->array($factory->getImportedNamespaces())->isEqualTo(array('foo' => 'foo'))
				->object($factory->importNamespace('foo', 'bar'))->isIdenticalTo($factory)
				->array($factory->getImportedNamespaces())->isEqualTo(array('foo' => 'foo', 'bar' => 'foo'))
				->object($factory->importNamespace('foo\bar', 'truc'))->isIdenticalTo($factory)
				->array($factory->getImportedNamespaces())->isEqualTo(array('foo' => 'foo', 'bar' => 'foo', 'truc' => 'foo\bar'))
				->object($factory->importNamespace('foo\bar\toto', 'tutu'))->isIdenticalTo($factory)
				->array($factory->getImportedNamespaces())->isEqualTo(array('foo' => 'foo', 'bar' => 'foo', 'truc' => 'foo\bar', 'tutu' => 'foo\bar\toto'))
				->exception(function() use ($factory) { $factory->importNamespace('foo\bar\tutu'); })
					->isInstanceOf('mageekguy\atoum\factory\exception')
					->hasMessage('Unable to use \'foo\bar\tutu\' as \'tutu\' because the name is already in use')
			->if($factory->setCurrentClass(__CLASS__))
			->then
				->array($factory->getImportedNamespaces())->isEmpty()
				->object($factory->importNamespace('foo'))->isIdenticalTo($factory)
				->array($factory->getImportedNamespaces())->isEqualTo(array('foo' => 'foo'))
				->object($factory->importNamespace('\foo'))->isIdenticalTo($factory)
				->array($factory->getImportedNamespaces())->isEqualTo(array('foo' => 'foo'))
				->object($factory->importNamespace('foo', 'bar'))->isIdenticalTo($factory)
				->array($factory->getImportedNamespaces())->isEqualTo(array('foo' => 'foo', 'bar' => 'foo'))
				->object($factory->importNamespace('foo\bar', 'truc'))->isIdenticalTo($factory)
				->array($factory->getImportedNamespaces())->isEqualTo(array('foo' => 'foo', 'bar' => 'foo', 'truc' => 'foo\bar'))
				->object($factory->importNamespace('foo\bar\toto', 'tutu'))->isIdenticalTo($factory)
				->array($factory->getImportedNamespaces())->isEqualTo(array('foo' => 'foo', 'bar' => 'foo', 'truc' => 'foo\bar', 'tutu' => 'foo\bar\toto'))
				->exception(function() use ($factory) { $factory->importNamespace('foo\bar\tutu'); })
					->isInstanceOf('mageekguy\atoum\factory\exception')
					->hasMessage('Unable to use \'foo\bar\tutu\' as \'tutu\' because the name is already in use')
		;
	}
}

?>
