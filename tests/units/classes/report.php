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
		$this->assert
			->testedClass
				->isSubclassOf('mageekguy\atoum\observer')
				->isSubclassOf('mageekguy\atoum\adapter\aggregator')
		;
	}

	public function test__construct()
	{
		$this->assert
			->if($report = new atoum\report())
			->then
				->variable($report->getTitle())->isNull()
				->object($report->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($report->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->array($report->getFields())->isEmpty()
				->array($report->getWriters())->isEmpty()
			->if($report = new atoum\report($locale = new atoum\locale(), $adapter = new atoum\adapter()))
			->then
				->variable($report->getTitle())->isNull()
				->object($report->getLocale())->isIdenticalTo($locale)
				->object($report->getAdapter())->isIdenticalTo($adapter)
				->array($report->getFields())->isEmpty()
				->array($report->getWriters())->isEmpty()
		;
	}

	public function testSetTitle()
	{
		$this->assert
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

		$this->assert
			->if($report = new atoum\report())
			->then
				->object($report->setLocale($locale = new atoum\locale()))->isIdenticalTo($report)
				->object($report->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testAddField()
	{
		$this
			->mock('mageekguy\atoum\report\field')
			->assert
				->if($report = new atoum\report())
				->then
					->object($report->addField($field = new \mock\mageekguy\atoum\report\field))->isIdenticalTo($report)
					->array($report->getFields())->isIdenticalTo(array($field))
					->object($report->addField($otherField = new \mock\mageekguy\atoum\report\field()))->isIdenticalTo($report)
					->array($report->getFields())->isIdenticalTo(array($field, $otherField))
		;
	}
}

?>
