<?php

namespace mageekguy\atoum\tests\units\writers;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\writers,
	\mageekguy\atoum\mailers
;

require_once(__DIR__ . '/../../runner.php');

class mail extends atoum\test
{
	public function test__construct()
	{
		$writer = new writers\mail();

		$this->assert
			->object($writer->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
			->object($writer->getMailer())->isInstanceOf('\mageekguy\atoum\mailers\mail')
		;
	}

	public function testClass()
	{
		$this->assert
			->class('\mageekguy\atoum\writers\mail')
				->isSubclassOf('\mageekguy\atoum\writer')
				->hasInterface('\mageekguy\atoum\report\writers\asynchronous')
		;
	}

	public function testSetMailer()
	{
		$writer = new writers\mail();

		$this->assert
			->object($writer->setMailer($mailer = new mailers\mail()))->isIdenticalTo($writer)
			->object($writer->getMailer())->isIdenticalTo($mailer)
		;
	}

	public function testWrite()
	{

		$this->mock('\mageekguy\atoum\mailer');

		$mailer = new mock\mageekguy\atoum\mailer();
		$mailer->getMockController()->send = $mailer;

		$writer = new writers\mail();
		$writer->setMailer($mailer);

		$this->assert
			->object($writer->write($something = uniqid()))->isIdenticalTo($writer)
			->mock($mailer)->call('send', array($something))
		;
	}

	public function testAsynchronousWrite()
	{
		$this->mock($this->getTestedClassName());

		$writer = new mock\mageekguy\atoum\writers\mail();

		$writer->getMockController()->write = $writer;

		$this->mock('\mageekguy\atoum\reports\asynchronous');

		$this->assert
			->object($writer->asynchronousWrite($report = new mock\mageekguy\atoum\reports\asynchronous()))->isIdenticalTo($writer)
			->mock($writer)->call('write', array((string) $report))
		;
	}
}

?>
