<?php

namespace mageekguy\atoum\tests\units\fcgi\records;

use
	mageekguy\atoum,
	mageekguy\atoum\fcgi\records\requests,
	mageekguy\atoum\fcgi\records\collection as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class collection extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->hasInterface('iterator')
			->hasInterface('countable')
			->hasInterface('arrayAccess')
		;
	}

	public function test__construct()
	{
		$this
			->if($collection = new testedClass())
			->then
				->sizeOf($collection)->isZero()
				->variable($collection->getRequestId())->isNull()
		;
	}

	public function testSetRecord()
	{
		$this
			->if($collection = new testedClass())
			->then
				->object($collection->setRecord($record = new requests\begin()))->isIdenticalTo($collection)
				->object($collection->getRecord(0))->isIdenticalTo($record)
				->sizeOf($collection)->isEqualTo(1)
				->string($collection->getRequestId())->isEqualTo($record->getRequestId())
				->object($collection->setRecord($otherRecord = new requests\begin()))->isIdenticalTo($collection)
				->object($collection->getRecord(0))->isIdenticalTo($record)
				->object($collection->getRecord(1))->isIdenticalTo($otherRecord)
				->sizeOf($collection)->isEqualTo(2)
				->object($collection->setRecord($otherRecord, 0))->isIdenticalTo($collection)
				->object($collection->setRecord($record, 1))->isIdenticalTo($collection)
				->object($collection->getRecord(0))->isIdenticalTo($otherRecord)
				->object($collection->getRecord(1))->isIdenticalTo($record)
				->sizeOf($collection)->isEqualTo(2)
				->exception(function() use ($collection, & $record) { $collection->setRecord($record = new requests\begin(requests\begin::responder, false, rand(2, 65535))); })
					->isInstanceOf('mageekguy\atoum\fcgi\records\collection\exception')
					->hasMessage('Unable to set record with request ID \'' . $record->getRequestId() . '\' in a collection with request ID \'' . $collection->getRequestId() . '\'')
		;
	}

	public function testGetRecord()
	{
		$this
			->if($collection = new testedClass())
			->then
				->variable($collection->getRecord(rand(0, PHP_INT_MAX)))->isNull()
		;
	}
}
