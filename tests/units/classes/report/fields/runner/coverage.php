<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use
	mageekguy\atoum,
	mock\mageekguy\atoum\report\fields\runner\coverage as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class coverage extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\field');
	}

	public function test__construct()
	{
		$this
			->if($field = new testedClass())
			->then
				->object($field->getPhp())->isEqualTo(new atoum\php())
				->object($field->getAdapter())->isEqualTo(new atoum\adapter())
				->array($field->getSrcDirectories())->isEmpty()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
				->variable($field->getCoverage())->isNull()
		;
	}

	public function testSetPhp()
	{
		$this
			->if($field = new testedClass())
			->then
				->object($field->setPhp($php = new atoum\php()))->isIdenticalTo($field)
				->object($field->getPhp())->isIdenticalTo($php)
				->object($field->setPhp())->isIdenticalTo($field)
				->object($field->getPhp())
					->isNotIdenticalTo($php)
					->isEqualTo(new atoum\php())
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($field = new testedClass())
			->then
				->object($field->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($field)
				->object($field->getAdapter())->isIdenticalTo($adapter)
				->object($field->setAdapter())->isIdenticalTo($field)
				->object($field->getAdapter())
					->isNotIdenticalTo($adapter)
					->isEqualTo(new atoum\adapter())
		;
	}

	public function testAddSrcDirectory()
	{
		$this
			->if($field = new testedClass())
			->then
				->object($field->addSrcDirectory($srcDirectory = uniqid()))->isIdenticalTo($field)
				->array($field->getSrcDirectories())->isEqualTo(array($srcDirectory => array()))
				->object($field->addSrcDirectory($srcDirectory))->isIdenticalTo($field)
				->array($field->getSrcDirectories())->isEqualTo(array($srcDirectory => array()))
				->object($field->addSrcDirectory($otherSrcDirectory = rand(1, PHP_INT_MAX)))->isIdenticalTo($field)
				->array($field->getSrcDirectories())->isIdenticalTo(array($srcDirectory => array(), (string) $otherSrcDirectory => array()))
				->object($field->addSrcDirectory($srcDirectory, $closure = function() {}))->isIdenticalTo($field)
				->array($field->getSrcDirectories())->isIdenticalTo(array($srcDirectory => array($closure), (string) $otherSrcDirectory => array()))
				->object($field->addSrcDirectory($srcDirectory, $otherClosure = function() {}))->isIdenticalTo($field)
				->array($field->getSrcDirectories())->isIdenticalTo(array($srcDirectory => array($closure, $otherClosure), (string) $otherSrcDirectory => array()))
		;
	}
}
