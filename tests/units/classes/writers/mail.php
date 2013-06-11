<?php

namespace mageekguy\atoum\tests\units\writers;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\writers,
	mageekguy\atoum\mailers
;

require_once __DIR__ . '/../../runner.php';

class mail extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($writer = new writers\mail())
			->then
				->object($writer->getMailer())->isInstanceOf('mageekguy\atoum\mailers\mail')
				->object($writer->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($writer->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->if($writer = new writers\mail($mailer = new atoum\mailers\mail(), $locale = new atoum\locale(), $adapter = new atoum\adapter()))
			->then
				->object($writer->getMailer())->isIdenticalTo($mailer)
				->object($writer->getLocale())->isIdenticalTo($locale)
				->object($writer->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testClass()
	{
		$this
			->testedClass
				->extends('mageekguy\atoum\writer')
				->implements('mageekguy\atoum\report\writers\asynchronous')
		;
	}

	public function testSetMailer()
	{
		$this
			->if($writer = new writers\mail())
			->then
				->object($writer->setMailer($mailer = new mailers\mail()))->isIdenticalTo($writer)
				->object($writer->getMailer())->isIdenticalTo($mailer)
		;
	}

	public function testSetLocale()
	{
		$this
			->if($writer = new writers\mail())
			->then
				->object($writer->setLocale($locale = new atoum\locale()))->isIdenticalTo($writer)
				->object($writer->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testWrite()
	{
		$this
			->if($mailer = new \mock\mageekguy\atoum\mailer())
			->and($mailer->getMockController()->send = $mailer)
			->and($writer = new writers\mail())
			->and($writer->setMailer($mailer))
			->then
				->object($writer->write($something = uniqid()))->isIdenticalTo($writer)
				->mock($mailer)->call('send')->withArguments($something)->once()
		;
	}

	public function testWriteAsynchronousReport()
	{
		$this
			->if($mailer = new atoum\mailers\mail())
			->and($writer = new \mock\mageekguy\atoum\writers\mail($mailer, $locale = new \mock\mageekguy\atoum\locale(), $adapter = new atoum\test\adapter()))
			->and($writer->getMockController()->write = $writer)
			->and($adapter->date = function($arg) { return $arg; })
			->then
				->object($writer->writeAsynchronousReport($report = new \mock\mageekguy\atoum\reports\asynchronous()))->isIdenticalTo($writer)
				->mock($writer)->call('write')->withArguments((string) $report)->once()
				->string($mailer->getSubject())->isEqualTo('Unit tests report, the Y-m-d at H:i:s')
				->mock($locale)
					->call('_')->withArguments('Unit tests report, the %1$s at %2$s')->once()
					->call('_')->withArguments('Y-m-d')->once()
					->call('_')->withArguments('H:i:s')->once()
			->if($mailer = new atoum\mailers\mail())
			->and($writer = new \mock\mageekguy\atoum\writers\mail($mailer, $locale = new \mock\mageekguy\atoum\locale(), $adapter = new atoum\test\adapter()))
			->and($writer->getMockController()->write = $writer)
			->then
				->object($writer->writeAsynchronousReport($report->setTitle($title = uniqid())))->isIdenticalTo($writer)
				->mock($writer)->call('write')->withArguments((string) $report)->once()
				->string($mailer->getSubject())->isEqualTo($title)
				->mock($locale)->call('_')->never()
			->if($mailer->setSubject($mailerSubject = uniqid()))
			->and($this->resetMock($writer))
			->then
				->object($writer->writeAsynchronousReport($report))->isIdenticalTo($writer)
				->mock($writer)->call('write')->withArguments((string) $report)->once()
				->string($mailer->getSubject())->isEqualTo($mailerSubject)
		;
	}
}
