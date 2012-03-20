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
				->array($factory->getImportations())->isEmpty()
		;
	}

	public function testBuild()
	{
		$this->assert
			->if($factory = new atoum\factory())
			->then
				->object($arrayIterator = $factory->build('arrayIterator'))->isEqualTo(new \arrayIterator())
				->object($arrayIterator = $factory->build('arrayIterator', array()))->isEqualTo(new \arrayIterator())
				->object($arrayIterator = $factory->build('arrayIterator', array(array())))->isEqualTo(new \arrayIterator())
				->object($arrayIterator = $factory->build('arrayIterator', array($data = array(uniqid(), uniqid(), uniqid()))))->isEqualTo(new \arrayIterator($data))
				->exception(function() use ($factory, & $class) { $factory->build($class = uniqid()); })
					->isInstanceOf('mageekguy\atoum\factory\exception')
					->hasMessage('Unable to build an instance of class \'' . $class . '\' because class does not exist')
			->if($factory->setBuilder('arrayIterator', function() use (& $return) { return ($return = new \arrayIterator); }))
				->object($arrayIterator = $factory->build('arrayIterator'))->isIdenticalTo($return)
				->object($arrayIterator = $factory->build('arrayIterator', array()))->isIdenticalTo($return)
				->object($arrayIterator = $factory->build('arrayIterator', array(array())))->isIdenticalTo($return)
				->object($arrayIterator = $factory->build('arrayIterator', array($data = array(uniqid(), uniqid(), uniqid()))))->isIdenticalTo($return)
			->if($factory->setBuilder('arrayIterator', function() use (& $otherReturn) { return ($otherReturn = new \arrayIterator); }, __CLASS__))
				->object($arrayIterator = $factory->build('arrayIterator'))->isIdenticalTo($return)
				->object($arrayIterator = $factory->build('arrayIterator', array()))->isIdenticalTo($return)
				->object($arrayIterator = $factory->build('arrayIterator', array(array())))->isIdenticalTo($return)
				->object($arrayIterator = $factory->build('arrayIterator', array($data = array(uniqid(), uniqid(), uniqid()))))->isIdenticalTo($return)
				->object($arrayIterator = $factory->build('arrayIterator', array(), __CLASS__))->isIdenticalTo($otherReturn)
				->object($arrayIterator = $factory->build('arrayIterator', array(array()), __CLASS__))->isIdenticalTo($otherReturn)
				->object($arrayIterator = $factory->build('arrayIterator', array($data = array(uniqid(), uniqid(), uniqid())), __CLASS__))->isIdenticalTo($otherReturn)
			->if($factory->setBuilder('arrayIterator', function() { return uniqid(); }))
			->then
				->exception(function() use ($factory) { $factory->build('arrayIterator'); })
					->isInstanceOf('mageekguy\atoum\factory\exception')
					->hasMessage('Unable to build an instance of class \'arrayIterator\' with current builder')
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
		;
	}

	public function testReturnWhenBuild()
	{
		$this->assert
			->if($factory = new atoum\factory())
			->and($arrayIterator = new \arrayIterator())
			->then
				->object($factory->returnWhenBuild('arrayIterator', $arrayIterator))->isIdenticalTo($factory)
				->boolean($factory->builderIsSet('arrayIterator'))->isTrue()
				->object($factory->build('arrayIterator'))->isIdenticalTo($arrayIterator)
		;
	}

	public function testGetBuilder()
	{
		$this->assert
			->if($factory = new atoum\factory())
			->then
				->variable($factory->getBuilder('arrayIterator', uniqid()))->isNull()
				->variable($factory->getBuilder('arrayIterator'))->isNull()
			->if($factory->setBuilder('arrayIterator', $closure = function() {}))
			->then
				->object($factory->getBuilder('arrayIterator', uniqid()))->isIdenticalTo($closure)
				->object($factory->getBuilder('arrayIterator'))->isIdenticalTo($closure)
			->if($factory->setBuilder('arrayIterator', $otherClosure = function() {}, $class = uniqid()))
			->then
				->object($factory->getBuilder('arrayIterator', $class))->isIdenticalTo($otherClosure)
				->object($factory->getBuilder('arrayIterator', uniqid()))->isIdenticalTo($closure)
				->object($factory->getBuilder('arrayIterator'))->isIdenticalTo($closure)
		;
	}

	public function testImport()
	{
		$this->assert
			->if($factory = new atoum\factory())
			->then
				->object($factory->import('foo'))->isIdenticalTo($factory)
				->array($factory->getImportations())->isEqualTo(array('foo' => 'foo'))
				->object($factory->import('\foo'))->isIdenticalTo($factory)
				->array($factory->getImportations())->isEqualTo(array('foo' => 'foo'))
				->object($factory->import('foo', 'bar'))->isIdenticalTo($factory)
				->array($factory->getImportations())->isEqualTo(array('foo' => 'foo', 'bar' => 'foo'))
				->object($factory->import('foo\bar', 'truc'))->isIdenticalTo($factory)
				->array($factory->getImportations())->isEqualTo(array('foo' => 'foo', 'bar' => 'foo', 'truc' => 'foo\bar'))
				->object($factory->import('foo\bar\toto', 'tutu'))->isIdenticalTo($factory)
				->array($factory->getImportations())->isEqualTo(array('foo' => 'foo', 'bar' => 'foo', 'truc' => 'foo\bar', 'tutu' => 'foo\bar\toto'))
				->exception(function() use ($factory) { $factory->import('foo\bar\tutu'); })
					->isInstanceOf('mageekguy\atoum\factory\exception')
					->hasMessage('Unable to use \'foo\bar\tutu\' as \'tutu\' because the name is already in use')
		;
	}

	public function testResetImportations()
	{
		$this->assert
			->if($factory = new atoum\factory())
			->then
				->object($factory->resetImportations())->isIdenticalTo($factory)
				->array($factory->getImportations())->isEmpty()
			->if($factory->import($this, uniqid()))
			->then
				->object($factory->resetImportations())->isIdenticalTo($factory)
				->array($factory->getImportations())->isEmpty()
			->if($factory->import($this, uniqid(), uniqid()))
			->then
				->object($factory->resetImportations())->isIdenticalTo($factory)
				->array($factory->getImportations())->isEmpty()
		;
	}
}

?>
