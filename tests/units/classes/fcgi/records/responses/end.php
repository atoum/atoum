<?php

namespace mageekguy\atoum\tests\units\fcgi\records\responses;

use
	mageekguy\atoum,
	mageekguy\atoum\fcgi\records\responses\end as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class end extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\fcgi\records\response');
	}

	public function testClassConstants()
	{
		$this
			->string(testedClass::type)->isEqualTo(3)
			->string(testedClass::requestComplete)->isEqualTo('0')
			->string(testedClass::serverCanNotMultiplexConnection)->isEqualTo(1)
			->string(testedClass::serverIsOverloaded)->isEqualTo(2)
			->string(testedClass::serverDoesNotKnowRole)->isEqualTo(3)
		;
	}

	public function test__construct()
	{
		$this
			->if($record = new testedClass($requestId = rand(1, 255), $contentData = "\000\000\000\000" . chr(testedClass::requestComplete) . "\000\000\000"))
			->then
				->string($record->getType())->isEqualTo(testedClass::type)
				->string($record->getRequestId())->isEqualTo($requestId)
			->exception(function() { new testedClass(uniqid(), ''); })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Content data are invalid')
			->exception(function() { new testedClass(rand(1, 255), "\000\000\000\000" . chr(testedClass::serverCanNotMultiplexConnection) . "\000\000\000"); })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Server can not multiplex connection')
			->exception(function() { new testedClass(rand(1, 255), "\000\000\000\000" . chr(testedClass::serverIsOverloaded) . "\000\000\000"); })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Server is overloaded')
			->exception(function() { new testedClass(rand(1, 255), "\000\000\000\000" . chr(testedClass::serverDoesNotKnowRole) . "\000\000\000"); })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Server does not know the role')
		;
	}
}
