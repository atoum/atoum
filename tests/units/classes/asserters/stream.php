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
			->object($asserter->getGenerator())->isIdenticalTo($generator)
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\stream(new asserter\generator($this));

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
		$asserter = new asserters\stream(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->isRead();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Stream is undefined')
		;

		$streamController = atoum\mock\stream::get($streamName = uniqid());
		$streamController->file_get_contents = uniqid();

		$asserter->setWith($streamName);

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isRead(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage = sprintf($test->getLocale()->_('stream %s is not read'), $streamName))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isRead()',
						'fail' => $failMessage
					)
				)
			)
			->when(function() use ($streamName) { file_get_contents('atoum://' . $streamName); })
				->object($asserter->isRead())->isIdenticalTo($asserter)
				->integer($score->getPassNumber())->isEqualTo(1)
				->integer($score->getFailNumber())->isEqualTo(1)
		;
	}

	public function testIsWrited()
	{
		$asserter = new asserters\stream(new asserter\generator($this));

	}
}

?>
