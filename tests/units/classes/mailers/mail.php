<?php

namespace mageekguy\atoum\tests\units\mailers;

use
	mageekguy\atoum,
	mageekguy\atoum\mailers
;

require_once __DIR__ . '/../../runner.php';

class mail extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())
				->isSubClassOf('mageekguy\atoum\mailer')
			->string(mailers\mail::eol)->isEqualTo("\r\n")
		;
	}

	public function test__construct()
	{
		$mail = new mailers\mail();

		$this->assert
			->variable($mail->getTo())->isNull()
			->variable($mail->getFrom())->isNull()
			->variable($mail->getSubject())->isNull()
			->variable($mail->getReplyTo())->isNull()
			->variable($mail->getXMailer())->isNull()
		;

		$adapter = new atoum\test\adapter();

		$mail = new mailers\mail($adapter);

		$this->assert
			->object($mail->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testAddTo()
	{
		$mail = new mailers\mail();

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
		$mail = new mailers\mail();

		$this->assert
			->object($mail->setSubject($subject = uniqid()))->isIdenticalTo($mail)
			->string($mail->getSubject())->isEqualTo($subject)
		;

		$this->assert
			->object($mail->setSubject($subject = rand(1, PHP_INT_MAX)))->isIdenticalTo($mail)
			->string($mail->getSubject())->isEqualTo((string) $subject)
		;
	}

	public function testSetFrom()
	{
		$mail = new mailers\mail();

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
		$mail = new mailers\mail();

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

	public function testSetXMailer()
	{
		$mail = new mailers\mail();

		$this->assert
			->object($mail->setXMailer($mailer = uniqid()))->isIdenticalTo($mail)
			->string($mail->getXMailer())->isEqualTo($mailer)
		;

		$this->assert
			->object($mail->setXMailer($mailer = rand(1, PHP_INT_MAX)))->isIdenticalTo($mail)
			->string($mail->getXMailer())->isEqualTo((string) $mailer)
		;
	}

	public function testSetContentType()
	{
		$mail = new mailers\mail();

		$this->assert
			->object($mail->setContentType($type = 'text/plain', $charset = 'utf-8'))->isIdenticalTo($mail)
			->array($mail->getContentType())->isEqualTo(array($type, $charset))
		;
	}

	public function testSend()
	{
		$adapter = new atoum\test\adapter();
		$adapter->mail = function() {};

		$mail = new mailers\mail($adapter);

		$this->assert
			->exception(function() use ($mail) { $mail->send(uniqid()); })
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('To is undefined')
		;

		$mail->addTo($to = uniqid());

		$this->assert
			->exception(function() use ($mail) { $mail->send(uniqid()); })
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Subject is undefined')
		;

		$mail->setSubject($subject = uniqid());

		$this->assert
			->exception(function() use ($mail) { $mail->send(uniqid()); })
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('From is undefined')
		;

		$mail->setFrom($from = uniqid());

		$this->assert
			->exception(function() use ($mail) { $mail->send(uniqid()); })
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Reply to is undefined')
		;

		$mail->setReplyTo($replyTo = uniqid());

		$this->assert
			->exception(function() use ($mail) { $mail->send(uniqid()); })
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('X-mailer is undefined')
		;

		$mail->setXMailer($mailer = uniqid());

		$this->assert
			->object($mail->send($message = uniqid()))->isIdenticalTo($mail)
			->adapter($adapter)->call('mail')->withArguments($mail->getTo(), $mail->getSubject(), $message, 'From: ' . $from . "\r\n" . 'Reply-To: ' . $replyTo . "\r\n" .  'X-Mailer: ' . $mailer)->once();
		;

		$mail->setContentType($type = uniqid(), $charset = uniqid());

		$this->assert
			->object($mail->send($message = uniqid()))->isIdenticalTo($mail)
			->adapter($adapter)->call('mail')->withArguments($mail->getTo(), $mail->getSubject(), $message, 'From: ' . $from . "\r\n" . 'Reply-To: ' . $replyTo . "\r\n" .  'X-Mailer: ' . $mailer . "\r\n" . 'Content-Type: ' . $type . '; charset="' . $charset . '"')->once();
		;
	}
}
