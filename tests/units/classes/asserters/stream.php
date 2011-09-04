<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class stream extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\asserter')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\stream($generator = new asserter\generator($this));

		$this->assert
			->variable($asserter->getStreamName())->isNull()
			->variable($asserter->getStreamController())->isNull()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\stream($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->setWith($stream = uniqid()))->isIdenticalTo($asserter)
			->string($asserter->getStreamName())->isEqualTo($stream)
			->object($asserter->getStreamController())->isEqualTo(atoum\mock\stream::get($stream))
		;

		atoum\mock\stream::get($stream = uniqid());

		$this->assert
			->object($asserter->setWith($stream))->isIdenticalTo($asserter)
			->string($asserter->getStreamName())->isEqualTo($stream)
			->object($asserter->getStreamController())->isIdenticalTo(atoum\mock\stream::get($stream))
		;
	}

	public function testIsRead()
	{
		$asserter = new asserters\stream($generator = new asserter\generator($this));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->isRead();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Stream is undefined')
		;
	}
}

?>
