<?php

namespace mageekguy\atoum\tests\units\writers;

use
	\mageekguy\atoum,
	\mageekguy\atoum\writers
;

require_once(__DIR__ . '/../../runner.php');

class mail extends atoum\test
{
	public function test__construct()
	{
		$mail = new writers\mail();

		$this->assert
			->object($mail)->isInstanceOf('\mageekguy\atoum\adapter\aggregator')
			->object($mail->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
		;

		$adapter = new atoum\test\adapter();

		$mail = new writers\mail($adapter);

		$this->assert
			->object($mail->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testAddTo()
	{
		$mail = new writers\mail();

		$this->assert
			->object($mail->addTo($to1 = uniqid()))->isIdenticalTo($mail)
			->string($mail->getTo())->isEqualTo($to1)
			->object($mail->addTo($to2 = uniqid()))->isIdenticalTo($mail)
			->string($mail->getTo())->isEqualTo($to1 . ',' . $to2)
			->object($mail->addTo($to3 = uniqid(), $realName3 = uniqid()))->isIdenticalTo($mail)
			->string($mail->getTo())->isEqualTo($to1 . ',' . $to2 . ',' . $realName3 . ' <' . $to3 . '>')
		;
	}

	public function testSetSubject()
	{
		$mail = new writers\mail();

		$this->assert
			->object($mail->setSubject($subject = uniqid()))->isIdenticalTo($mail)
			->string($mail->getSubject())->isEqualTo($subject)
		;

		$this->assert
			->object($mail->setSubject($subject = rand(1, PHP_INT_MAX)))->isIdenticalTo($mail)
			->string($mail->getSubject())->isEqualTo((string) $subject)
		;
	}

	public function testSetMessage()
	{
		$mail = new writers\mail();

		$this->assert
			->object($mail->setMessage($message = uniqid()))->isIdenticalTo($mail)
			->string($mail->getMessage())->isEqualTo($message)
		;

		$this->assert
			->object($mail->setMessage($message = rand(1, PHP_INT_MAX)))->isIdenticalTo($mail)
			->string($mail->getMessage())->isEqualTo((string) $message)
		;
	}

	public function testSetFrom()
	{
		$mail = new writers\mail();

		$this->assert
			->object($mail->setFrom($from = uniqid()))->isIdenticalTo($mail)
			->string($mail->getFrom())->isEqualTo($from)
		;

		$this->assert
			->object($mail->setFrom($from = rand(1, PHP_INT_MAX)))->isIdenticalTo($mail)
			->string($mail->getFrom())->isEqualTo((string) $from)
		;

		$this->assert
			->object($mail->setFrom($from = uniqid(), $realName = uniqid()))->isIdenticalTo($mail)
			->string($mail->getFrom())->isEqualTo($realName . ' <' . $from . '>')
		;

	}

	public function testSetReplyTo()
	{
		$mail = new writers\mail();

		$this->assert
			->object($mail->setReplyTo($replyTo = uniqid()))->isIdenticalTo($mail)
			->string($mail->getReplyTo())->isEqualTo($replyTo)
		;

		$this->assert
			->object($mail->setReplyTo($replyTo = rand(1, PHP_INT_MAX)))->isIdenticalTo($mail)
			->string($mail->getReplyTo())->isEqualTo((string) $replyTo)
		;

		$this->assert
			->object($mail->setReplyTo($replyTo = uniqid(), $realName = uniqid()))->isIdenticalTo($mail)
			->string($mail->getReplyTo())->isEqualTo($realName . ' <' . $replyTo . '>')
		;
	}

	public function testSetMailer()
	{
		$mail = new writers\mail();

		$this->assert
			->object($mail->setMailer($mailer = uniqid()))->isIdenticalTo($mail)
			->string($mail->getMailer())->isEqualTo($mailer)
		;

		$this->assert
			->object($mail->setMailer($mailer = rand(1, PHP_INT_MAX)))->isIdenticalTo($mail)
			->string($mail->getMailer())->isEqualTo((string) $mailer)
		;
	}

	public function testSend()
	{
		$adapter = new atoum\test\adapter();

		$mail = new writers\mail($adapter);

		$adapter->mail = function() {};

		$this->assert
			->object($mail->send())->isIdenticalTo($mail)
		;
	}
}

?>
