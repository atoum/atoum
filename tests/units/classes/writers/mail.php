<?php

namespace atoum\tests\units\writers;

use
	atoum,
	atoum\mock,
	atoum\writers,
	atoum\mailers
;

require_once __DIR__ . '/../../runner.php';

class mail extends atoum\test
{
	public function test__construct()
	{
		$writer = new writers\mail();

		$this->assert
			->object($writer->getMailer())->isInstanceOf('atoum\mailers\mail')
			->object($writer->getLocale())->isInstanceOf('atoum\locale')
			->object($writer->getAdapter())->isInstanceOf('atoum\adapter')
		;

		$writer = new writers\mail($mailer = new atoum\mailers\mail(), $locale = new atoum\locale(), $adapter = new atoum\adapter());

		$this->assert
			->object($writer->getMailer())->isIdenticalTo($mailer)
			->object($writer->getLocale())->isIdenticalTo($locale)
			->object($writer->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubclassOf('atoum\writer')
				->hasInterface('atoum\report\writers\asynchronous')
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

	public function testSetLocale()
	{
		$writer = new writers\mail();

		$this->assert
			->object($writer->setLocale($locale = new atoum\locale()))->isIdenticalTo($writer)
			->object($writer->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testWrite()
	{
		$mailer = new \mock\atoum\mailer();
		$mailer->getMockController()->send = $mailer;

		$writer = new writers\mail();
		$writer->setMailer($mailer);

		$this->assert
			->object($writer->write($something = uniqid()))->isIdenticalTo($writer)
			->mock($mailer)->call('send')->withArguments($something)->once()
		;
	}

	public function testWriteAsynchronousReport()
	{
		$mailer = new atoum\mailers\mail();

		$writer = new \mock\atoum\writers\mail($mailer, $locale = new \mock\atoum\locale(), $adapter = new atoum\test\adapter());
		$writer->getMockController()->write = $writer;

		$adapter->date = function($arg) { return $arg; };

		$this->assert
			->object($writer->writeAsynchronousReport($report = new \mock\atoum\reports\asynchronous()))->isIdenticalTo($writer)
			->mock($writer)->call('write')->withArguments((string) $report)->once()
			->string($mailer->getSubject())->isEqualTo('Unit tests report, the Y-m-d at H:i:s')
			->mock($locale)
				->call('_')->withArguments('Unit tests report, the %1$s at %2$s')->once()
				->call('_')->withArguments('Y-m-d')->once()
				->call('_')->withArguments('H:i:s')->once()
		;

		$mailer = new atoum\mailers\mail();

		$writer = new \mock\atoum\writers\mail($mailer, $locale = new \mock\atoum\locale(), $adapter = new atoum\test\adapter());
		$writer->getMockController()->write = $writer;

		$this->assert
			->object($writer->writeAsynchronousReport($report->setTitle($title = uniqid())))->isIdenticalTo($writer)
			->mock($writer)->call('write')->withArguments((string) $report)->once()
			->string($mailer->getSubject())->isEqualTo($title)
			->mock($locale)->call('_')->never()
		;

		$mailer->setSubject($mailerSubject = uniqid());

		$this->assert
			->object($writer->writeAsynchronousReport($report))->isIdenticalTo($writer)
			->mock($writer)->call('write')->withArguments((string) $report)->once()
			->string($mailer->getSubject())->isEqualTo($mailerSubject)
		;
	}
}
