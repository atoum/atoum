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
			->if($collection = new testedClass(array($record = new requests\begin())))
			->then
				->object($collection->getRecord(0))->isIdenticalTo($record)
		;
	}

	public function testValid()
	{
		$this
			->if($collection = new testedClass())
			->then
				->boolean($collection->valid())->isFalse()
			->if($collection[] = $record = new requests\begin())
			->then
				->boolean($collection->valid())->isTrue()
			->if($collection->next())
			->then
				->boolean($collection->valid())->isFalse()
			->if($collection->rewind())
			->then
				->boolean($collection->valid())->isTrue()
		;
	}

	public function testNext()
	{
		$this
			->if($collection = new testedClass())
			->then
				->object($collection->next())->isIdenticalTo($collection)
				->variable($collection->key())->isNull()
				->variable($collection->current())->isNull()
			->if($collection[] = $record = new requests\begin())
			->then
				->object($collection->next())->isIdenticalTo($collection)
				->variable($collection->key())->isNull()
				->variable($collection->current())->isNull()
			->if($collection[] = $otherRecord = new requests\begin())
			->then
				->object($collection->next())->isIdenticalTo($collection)
				->variable($collection->key())->isNull()
				->variable($collection->current())->isNull()
			->if($collection->rewind())
			->then
				->object($collection->next())->isIdenticalTo($collection)
				->variable($collection->key())->isEqualTo(1)
				->variable($collection->current())->isIdenticalTo($otherRecord)
				->object($collection->next())->isIdenticalTo($collection)
				->variable($collection->key())->isNull()
				->variable($collection->current())->isNull()
		;
	}

	public function testKey()
	{
		$this
			->if($collection = new testedClass())
			->then
				->variable($collection->key())->isNull()
			->if($collection[] = new requests\begin())
			->then
				->integer($collection->key())->isZero()
			->if($collection->next())
			->then
				->variable($collection->key())->isNull()
			->if($collection[$key = uniqid()] = new requests\begin())
			->and($collection->rewind())
			->then
				->integer($collection->key())->isZero()
			->if($collection->next())
			->then
				->string($collection->key())->isEqualTo($key)
			->if($collection->next())
			->then
				->variable($collection->key())->isNull()
		;
	}

	public function testCurrent()
	{
		$this
			->if($collection = new testedClass())
			->then
				->variable($collection->current())->isNull()
			->if($collection[] = $record = new requests\begin())
			->then
				->object($collection->current())->isIdenticalTo($record)
			->if($collection->next())
			->then
				->variable($collection->current())->isNull()
		;
	}

	public function testRewind()
	{
		$this
			->if($collection = new testedClass())
			->then
				->object($collection->rewind())->isIdenticalTo($collection)
				->variable($collection->key())->isNull()
				->variable($collection->current())->isNull()
			->if($collection[] = $record = new requests\begin())
			->then
				->object($collection->rewind())->isIdenticalTo($collection)
				->integer($collection->key())->isZero()
				->object($collection->current())->isIdenticalTo($record)
			->if($collection[] = $otherRecord = new requests\begin())
			->then
				->object($collection->rewind())->isIdenticalTo($collection)
				->integer($collection->key())->isZero()
				->object($collection->current())->isIdenticalTo($record)
			->if($collection->next())
			->then
				->object($collection->rewind())->isIdenticalTo($collection)
				->integer($collection->key())->isZero()
				->object($collection->current())->isIdenticalTo($record)
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

	public function testOffsetSet()
	{
		$this
			->if($collection = new testedClass())
			->then
				->object($collection->offsetSet(0, $record = new requests\begin()))->isIdenticalTo($collection)
				->object($collection->getRecord(0))->isIdenticalTo($record)
				->sizeOf($collection)->isEqualTo(1)
				->string($collection->getRequestId())->isEqualTo($record->getRequestId())
				->object($collection->offsetSet(1, $otherRecord = new requests\begin()))->isIdenticalTo($collection)
				->object($collection->getRecord(0))->isIdenticalTo($record)
				->object($collection->getRecord(1))->isIdenticalTo($otherRecord)
				->sizeOf($collection)->isEqualTo(2)
				->object($collection->offsetSet(0, $otherRecord))->isIdenticalTo($collection)
				->object($collection->offsetSet(1, $record))->isIdenticalTo($collection)
				->object($collection->getRecord(0))->isIdenticalTo($otherRecord)
				->object($collection->getRecord(1))->isIdenticalTo($record)
				->sizeOf($collection)->isEqualTo(2)
				->exception(function() use ($collection, & $record) { $collection->offsetSet(0, $record = new requests\begin(requests\begin::responder, false, rand(2, 65535))); })
					->isInstanceOf('mageekguy\atoum\fcgi\records\collection\exception')
					->hasMessage('Unable to set record with request ID \'' . $record->getRequestId() . '\' in a collection with request ID \'' . $collection->getRequestId() . '\'')
		;
	}

	public function testGetRecord()
	{
		$this
			->if($collection = new testedClass())
			->then
				->variable($collection->getRecord(rand(- PHP_INT_MAX, PHP_INT_MAX)))->isNull()
			->if($collection[] = $record = new requests\begin())
			->then
				->object($collection->getRecord(0))->isIdenticalTo($record)
				->variable($collection->getRecord(rand(- PHP_INT_MAX,  -1)))->isNull()
				->variable($collection->getRecord(rand(1, PHP_INT_MAX)))->isNull()
			->if($collection[$key = uniqid()] = $otherRecord = new requests\begin())
			->then
				->object($collection->getRecord(0))->isIdenticalTo($record)
				->object($collection->getRecord($key))->isIdenticalTo($otherRecord)
				->variable($collection->getRecord(rand(1, PHP_INT_MAX)))->isNull()
		;
	}

	public function testOffsetGet()
	{
		$this
			->if($collection = new testedClass())
			->then
				->variable($collection->offsetGet(rand(- PHP_INT_MAX, PHP_INT_MAX)))->isNull()
			->if($collection[] = $record = new requests\begin())
			->then
				->object($collection->offsetGet(0))->isIdenticalTo($record)
				->variable($collection->offsetGet(rand(- PHP_INT_MAX,  -1)))->isNull()
				->variable($collection->offsetGet(rand(1, PHP_INT_MAX)))->isNull()
			->if($collection[$key = uniqid()] = $otherRecord = new requests\begin())
			->then
				->object($collection->offsetGet(0))->isIdenticalTo($record)
				->object($collection->offsetGet($key))->isIdenticalTo($otherRecord)
				->variable($collection->offsetGet(rand(1, PHP_INT_MAX)))->isNull()
		;
	}

	public function testRecordIsSet()
	{
		$this
			->if($collection = new testedClass())
			->then
				->boolean($collection->recordIsSet(rand(- PHP_INT_MAX, PHP_INT_MAX)))->isFalse()
				->boolean($collection->recordIsSet(uniqid()))->isFalse()
			->if($collection[] = $record = new requests\begin())
			->then
				->boolean($collection->recordIsSet(rand(- PHP_INT_MAX, -1)))->isFalse()
				->boolean($collection->recordIsSet(rand(1, PHP_INT_MAX)))->isFalse()
				->boolean($collection->recordIsSet(uniqid()))->isFalse()
				->boolean($collection->recordIsSet(0))->isTrue()
		;
	}

	public function testOffsetExists()
	{
		$this
			->if($collection = new testedClass())
			->then
				->boolean($collection->offsetExists(rand(- PHP_INT_MAX, PHP_INT_MAX)))->isFalse()
				->boolean($collection->offsetExists(uniqid()))->isFalse()
			->if($collection[] = $record = new requests\begin())
			->then
				->boolean($collection->offsetExists(rand(- PHP_INT_MAX, -1)))->isFalse()
				->boolean($collection->offsetExists(rand(1, PHP_INT_MAX)))->isFalse()
				->boolean($collection->offsetExists(uniqid()))->isFalse()
				->boolean($collection->offsetExists(0))->isTrue()
		;
	}

	public function testUnsetRecord()
	{
		$this
			->if($collection = new testedClass())
			->then
				->object($collection->unsetRecord(rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($collection)
				->object($collection->unsetRecord(uniqid()))->isIdenticalTo($collection)
			->if($collection[] = $record = new requests\begin())
			->then
				->object($collection->unsetRecord(rand(- PHP_INT_MAX, -1)))->isIdenticalTo($collection)
				->boolean($collection->recordIsSet(0))->isTrue()
				->object($collection->unsetRecord(uniqid()))->isIdenticalTo($collection)
				->boolean($collection->recordIsSet(0))->isTrue()
				->object($collection->unsetRecord(0))->isIdenticalTo($collection)
				->boolean($collection->recordIsSet(0))->isFalse()
		;
	}

	public function testOffsetUnset()
	{
		$this
			->if($collection = new testedClass())
			->then
				->object($collection->offsetUnset(rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($collection)
				->object($collection->offsetUnset(uniqid()))->isIdenticalTo($collection)
			->if($collection[] = $record = new requests\begin())
			->then
				->object($collection->offsetUnset(rand(- PHP_INT_MAX, -1)))->isIdenticalTo($collection)
				->boolean($collection->recordIsSet(0))->isTrue()
				->object($collection->offsetUnset(uniqid()))->isIdenticalTo($collection)
				->boolean($collection->recordIsSet(0))->isTrue()
				->object($collection->offsetUnset(0))->isIdenticalTo($collection)
				->boolean($collection->recordIsSet(0))->isFalse()
		;
	}

	public function testGetStreamData()
	{
		$this
			->if($collection = new testedClass())
			->then
				->string($collection->getStreamData())->isEmpty()
			->if($collection[] = $record = new requests\begin())
			->then
				->string($collection->getStreamData())->isEqualTo($record->getStreamData())
			->if($collection[] = $otherRecord = new requests\begin())
			->then
				->string($collection->getStreamData())->isEqualTo($record->getStreamData() . $otherRecord->getStreamData())
		;
	}
}
