<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class report extends atoum\test
{
	public function testTestedClass()
	{
		$this->testedClass
			->isSubclassOf('mageekguy\atoum\observer')
			->isSubclassOf('mageekguy\atoum\adapter\aggregator')
		;
	}

	public function test__construct()
	{
		$this
			->if($report = new atoum\report())
			->then
				->variable($report->getTitle())->isNull()
				->object($report->getFactory())->isInstanceOf('mageekguy\atoum\factory')
				->object($report->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($report->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->array($report->getFields())->isEmpty()
				->array($report->getWriters())->isEmpty()
			->if($factory = new atoum\factory())
			->and($factory['mageekguy\atoum\locale'] = $locale = new atoum\locale())
			->and($factory['mageekguy\atoum\adapter'] = $adapter = new atoum\adapter())
			->and($report = new atoum\report($factory))
			->then
				->variable($report->getTitle())->isNull()
				->object($report->getLocale())->isIdenticalTo($locale)
				->object($report->getAdapter())->isIdenticalTo($adapter)
				->object($report->getFactory()->build('mageekguy\atoum\locale'))->isIdenticalTo($locale)
				->object($report->getFactory()->build('mageekguy\atoum\adapter'))->isIdenticalTo($adapter)
				->array($report->getFields())->isEmpty()
				->array($report->getWriters())->isEmpty()
		;
	}

	public function testSetTitle()
	{
		$this
			->if($report = new atoum\report())
			->then
				->object($report->setTitle($title = uniqid()))->isEqualTo($report)
				->string($report->getTitle())->isEqualTo($title)
				->object($report->setTitle($title = rand(1, PHP_INT_MAX)))->isEqualTo($report)
				->string($report->getTitle())->isEqualTo((string) $title)
		;
	}

	public function testSetLocale()
	{

		$this
			->if($report = new atoum\report())
			->then
				->object($report->setLocale($locale = new atoum\locale()))->isIdenticalTo($report)
				->object($report->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testAddField()
	{
		$this
			->if($report = new atoum\report())
			->then
				->object($report->addField($field = new \mock\mageekguy\atoum\report\field))->isIdenticalTo($report)
				->array($report->getFields())->isIdenticalTo(array($field))
				->object($report->addField($otherField = new \mock\mageekguy\atoum\report\field()))->isIdenticalTo($report)
				->array($report->getFields())->isIdenticalTo(array($field, $otherField))
		;
	}
}
