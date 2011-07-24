<?php

namespace mageekguy\atoum\tests\units\writers;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\writers,
	mageekguy\atoum\mailers
;

require_once(__DIR__ . '/../../runner.php');

class mail extends atoum\test
{
	public function test__construct()
	{
		$writer = new writers\mail();

		$this->assert
			->object($writer->getMailer())->isInstanceOf('mageekguy\atoum\mailers\mail')
			->object($writer->getLocale())->isInstanceOf('mageekguy\atoum\locale')
			->object($writer->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
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
				->isSubclassOf('mageekguy\atoum\writer')
				->hasInterface('mageekguy\atoum\report\writers\asynchronous')
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
		$this->mockGenerator
			->generate('mageekguy\atoum\mailer')
		;

		$mailer = new \mock\mageekguy\atoum\mailer();
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
		$this->mockGenerator
			->generate($this->getTestedClassName())
			->generate('mageekguy\atoum\locale')
			->generate('mageekguy\atoum\reports\asynchronous')
		;

		$mailer = new atoum\mailers\mail();

		$writer = new \mock\mageekguy\atoum\writers\mail($mailer, $locale = new \mock\mageekguy\atoum\locale(), $adapter = new atoum\test\adapter());
		$writer->getMockController()->write = $writer;

		$adapter->date = function($arg) { return $arg; };

		$this->assert
			->object($writer->writeAsynchronousReport($report = new \mock\mageekguy\atoum\reports\asynchronous()))->isIdenticalTo($writer)
			->mock($writer)->call('write', array((string) $report))
			->string($mailer->getSubject())->isEqualTo('Unit tests report, the Y-m-d at H:i:s')
			->mock($locale)
				->call('_', array('Unit tests report, the %1$s at %2$s'))
				->call('_', array('Y-m-d'))
				->call('_', array('H:i:s'))
		;

		$mailer = new atoum\mailers\mail();

		$writer = new \mock\mageekguy\atoum\writers\mail($mailer, $locale = new \mock\mageekguy\atoum\locale(), $adapter = new atoum\test\adapter());
		$writer->getMockController()->write = $writer;

		$this->assert
			->object($writer->writeAsynchronousReport($report->setTitle($title = uniqid())))->isIdenticalTo($writer)
			->mock($writer)->call('write', array((string) $report))
			->string($mailer->getSubject())->isEqualTo($title)
			->mock($locale)->notCall('_')
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
