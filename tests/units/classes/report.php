<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\report as testedClass
;

require_once __DIR__ . '/../runner.php';

class report extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->implements('mageekguy\atoum\observer')
		;
	}

	public function test__construct()
	{
		$this
			->if($report = new testedClass())
			->then
				->variable($report->getTitle())->isNull()
				->object($report->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($report->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->array($report->getFields())->isEmpty()
				->array($report->getWriters())->isEmpty()
		;
	}

	public function testSetLocale()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->setLocale($locale = new atoum\locale()))->isIdenticalTo($report)
				->object($report->getLocale())->isIdenticalTo($locale)
				->object($report->setLocale())->isIdenticalTo($report)
				->object($report->getLocale())
					->isNotIdenticalTo($locale)
					->isEqualTo(new atoum\locale())
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($report)
				->object($report->getAdapter())->isIdenticalTo($adapter)
				->object($report->setAdapter())->isIdenticalTo($report)
				->object($report->getAdapter())
					->isNotIdenticalTo($adapter)
					->isEqualTo(new atoum\adapter())
		;
	}

	public function testSetTitle()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->setTitle($title = uniqid()))->isEqualTo($report)
				->string($report->getTitle())->isEqualTo($title)
				->object($report->setTitle($title = rand(1, PHP_INT_MAX)))->isEqualTo($report)
				->string($report->getTitle())->isEqualTo((string) $title)
		;
	}

	public function testAddField()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->addField($field = new \mock\mageekguy\atoum\report\field()))->isIdenticalTo($report)
				->array($report->getFields())->isIdenticalTo(array($field))
				->object($field->getLocale())->isIdenticalTo($report->getLocale())
				->object($report->addField($otherField = new \mock\mageekguy\atoum\report\field()))->isIdenticalTo($report)
				->array($report->getFields())->isIdenticalTo(array($field, $otherField))
				->object($field->getLocale())->isIdenticalTo($report->getLocale())
				->object($otherField->getLocale())->isIdenticalTo($report->getLocale())
		;
	}

	public function testResetField()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->resetFields())->isIdenticalTo($report)
				->array($report->getFields())->isEmpty()
			->if($report->addField(new \mock\mageekguy\atoum\report\field()))
			->and($report->addField(new \mock\mageekguy\atoum\report\field()))
			->then
				->object($report->resetFields())->isIdenticalTo($report)
				->array($report->getFields())->isEmpty()
		;
	}

	public function testIsOverridableBy()
	{
		$this
			->if($report = new testedClass())
			->and($otherReport = new testedClass())
			->then
				->boolean($report->isOverridableBy($report))->isFalse
				->boolean($report->isOverridableBy($otherReport))->isTrue
		;
	}
}
