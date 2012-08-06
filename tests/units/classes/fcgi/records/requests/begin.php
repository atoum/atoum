<?php

namespace mageekguy\atoum\tests\units\fcgi\records\requests;

use
	mageekguy\atoum,
	mageekguy\atoum\fcgi\records\requests\begin as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class begin extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\fcgi\records\request');
	}

	public function testClassConstants()
	{
		$this
			->string(testedClass::type)->isEqualTo(1)
			->string(testedClass::responder)->isEqualTo(1)
			->string(testedClass::authorizer)->isEqualTo(2)
			->string(testedClass::filter)->isEqualTo(3)
		;
	}

	public function test__construct()
	{
		$this
			->if($record = new testedClass())
			->then
				->string($record->getType())->isEqualTo(testedClass::type)
				->string($record->getRole())->isEqualTo(testedClass::responder)
				->boolean($record->connectionIsPersistent())->isFalse()
				->string($record->getRequestId())->isEqualTo(1)
				->string($record->getContentData())->isEqualTo("\000\001\000\000\000\000\000\000")
			->if($record = new testedClass(testedClass::authorizer))
				->string($record->getType())->isEqualTo(testedClass::type)
				->string($record->getRole())->isEqualTo(testedClass::authorizer)
				->boolean($record->connectionIsPersistent())->isFalse()
				->string($record->getRequestId())->isEqualTo(1)
				->string($record->getContentData())->isEqualTo("\000\002\000\000\000\000\000\000")
			->if($record = new testedClass(testedClass::filter))
				->string($record->getType())->isEqualTo(testedClass::type)
				->string($record->getRole())->isEqualTo(testedClass::filter)
				->boolean($record->connectionIsPersistent())->isFalse()
				->string($record->getRequestId())->isEqualTo(1)
				->string($record->getContentData())->isEqualTo("\000\003\000\000\000\000\000\000")
			->exception(function() use (& $role) { new testedClass($role = uniqid()); })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Role \'' . $role . '\' is invalid')
			->if($record = new testedClass(testedClass::responder, true))
				->string($record->getType())->isEqualTo(testedClass::type)
				->string($record->getRole())->isEqualTo(testedClass::responder)
				->boolean($record->connectionIsPersistent())->isTrue()
				->string($record->getRequestId())->isEqualTo(1)
				->string($record->getContentData())->isEqualTo("\000\001\001\000\000\000\000\000")
			->if($record = new testedClass(testedClass::responder, true, $requestId = rand(2, 65535)))
				->string($record->getType())->isEqualTo(testedClass::type)
				->string($record->getRole())->isEqualTo(testedClass::responder)
				->boolean($record->connectionIsPersistent())->isTrue()
				->string($record->getRequestId())->isEqualTo($requestId)
				->string($record->getContentData())->isEqualTo("\000\001\001\000\000\000\000\000")
		;
	}

	public function testSetConnectionPersistent()
	{
		$this
			->if($record = new testedClass())
			->then
				->object($record->setConnectionPersistent())->isIdenticalTo($record)
				->boolean($record->connectionIsPersistent())->isTrue()
				->string($record->getContentData())->isEqualTo("\000\001\001\000\000\000\000\000")
		;
	}

	public function testSetConnectionNotPersistent()
	{
		$this
			->if($record = new testedClass())
			->then
				->object($record->unsetConnectionPersistent())->isIdenticalTo($record)
				->boolean($record->connectionIsPersistent())->isFalse()
				->string($record->getContentData())->isEqualTo("\000\001\000\000\000\000\000\000")
			->if($record->setConnectionPersistent())
			->then
				->object($record->unsetConnectionPersistent())->isIdenticalTo($record)
				->boolean($record->connectionIsPersistent())->isFalse()
				->string($record->getContentData())->isEqualTo("\000\001\000\000\000\000\000\000")
		;
	}

	public function testSetRole()
	{
		$this
			->if($record = new testedClass())
			->then
				->object($record->setRole(testedClass::responder))->isIdenticalTo($record)
				->string($record->getRole())->isEqualTo(testedClass::responder)
				->string($record->getContentData())->isEqualTo("\000\001\000\000\000\000\000\000")
				->object($record->setRole(testedClass::authorizer))->isIdenticalTo($record)
				->string($record->getRole())->isEqualTo(testedClass::authorizer)
				->string($record->getContentData())->isEqualTo("\000\002\000\000\000\000\000\000")
				->object($record->setRole(testedClass::filter))->isIdenticalTo($record)
				->string($record->getRole())->isEqualTo(testedClass::filter)
				->string($record->getContentData())->isEqualTo("\000\003\000\000\000\000\000\000")
			->exception(function() use ($record, & $role) { $record->setRole($role = uniqid()); })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Role \'' . $role . '\' is invalid')
		;
	}
}
