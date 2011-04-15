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
			->object($writer->getMailer())->isInstanceOf('\mageekguy\atoum\mailers\mail')
			->object($writer->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
		;

		$writer = new writers\mail($mailer = new atoum\mailers\mail(), $adapter = new atoum\adapter());

		$this->assert
			->object($writer->getMailer())->isIdenticalTo($mailer)
			->object($writer->getAdapter())->isIdenticalTo($adapter)
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

	public function testWriteAsynchronousReport()
	{
		$this
			->mock($this->getTestedClassName())
			->mock('\mageekguy\atoum\reports\asynchronous')
		;

		$mailer = new atoum\mailers\mail();

		$writer = new mock\mageekguy\atoum\writers\mail($mailer);

		$writer->getMockController()->write = $writer;

		$report = new mock\mageekguy\atoum\reports\asynchronous();

		$this->assert
			->object($writer->writeAsynchronousReport($report))->isIdenticalTo($writer)
			->mock($writer)->call('write', array((string) $report))
			->variable($mailer->getSubject())->isNull()
		;

		$report->setTitle($title = uniqid());

		$this->assert
			->object($writer->writeAsynchronousReport($report))->isIdenticalTo($writer)
			->mock($writer)->call('write', array((string) $report))
			->string($mailer->getSubject())->isEqualTo($title)
		;

		$mailer->setSubject($mailerSubject = uniqid());

		$this->assert
			->object($writer->writeAsynchronousReport($report))->isIdenticalTo($writer)
			->mock($writer)->call('write', array((string) $report))
			->string($mailer->getSubject())->isEqualTo($mailerSubject)
		;
	}
}

?>
