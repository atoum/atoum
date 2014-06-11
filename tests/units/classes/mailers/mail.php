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
		$this
			->class($this->getTestedClassName())
				->extends('mageekguy\atoum\mailer')
			->string(mailers\mail::eol)->isEqualTo("\r\n")
		;
	}

	public function test__construct()
	{
		$this
			->if($mail = new mailers\mail())
			->then
				->variable($mail->getTo())->isNull()
				->variable($mail->getFrom())->isNull()
				->variable($mail->getSubject())->isNull()
				->variable($mail->getReplyTo())->isNull()
				->variable($mail->getXMailer())->isNull()
			->if($adapter = new atoum\test\adapter())
			->and($mail = new mailers\mail($adapter))
			->then
				->object($mail->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testAddTo()
	{
		$this
			->if($mail = new mailers\mail())
			->then
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
		$this
			->if($mail = new mailers\mail())
			->then
				->object($mail->setSubject($subject = uniqid()))->isIdenticalTo($mail)
				->string($mail->getSubject())->isEqualTo($subject)
				->object($mail->setSubject($subject = rand(1, PHP_INT_MAX)))->isIdenticalTo($mail)
				->string($mail->getSubject())->isEqualTo((string) $subject)
		;
	}

	public function testSetFrom()
	{
		$this
			->if($mail = new mailers\mail())
			->then
				->object($mail->setFrom($from = uniqid()))->isIdenticalTo($mail)
				->string($mail->getFrom())->isEqualTo($from)
				->object($mail->setFrom($from = rand(1, PHP_INT_MAX)))->isIdenticalTo($mail)
				->string($mail->getFrom())->isEqualTo((string) $from)
				->object($mail->setFrom($from = uniqid(), $realName = uniqid()))->isIdenticalTo($mail)
				->string($mail->getFrom())->isEqualTo($realName . ' <' . $from . '>')
		;
	}

	public function testSetReplyTo()
	{
		$this
			->if($mail = new mailers\mail())
			->then
				->object($mail->setReplyTo($replyTo = uniqid()))->isIdenticalTo($mail)
				->string($mail->getReplyTo())->isEqualTo($replyTo)
				->object($mail->setReplyTo($replyTo = rand(1, PHP_INT_MAX)))->isIdenticalTo($mail)
				->string($mail->getReplyTo())->isEqualTo((string) $replyTo)
				->object($mail->setReplyTo($replyTo = uniqid(), $realName = uniqid()))->isIdenticalTo($mail)
				->string($mail->getReplyTo())->isEqualTo($realName . ' <' . $replyTo . '>')
		;
	}

	public function testSetXMailer()
	{
		$this
			->if($mail = new mailers\mail())
			->then
				->object($mail->setXMailer($mailer = uniqid()))->isIdenticalTo($mail)
				->string($mail->getXMailer())->isEqualTo($mailer)
				->object($mail->setXMailer($mailer = rand(1, PHP_INT_MAX)))->isIdenticalTo($mail)
				->string($mail->getXMailer())->isEqualTo((string) $mailer)
		;
	}

	public function testSetContentType()
	{
		$this
			->if($mail = new mailers\mail())
			->then
				->object($mail->setContentType($type = 'text/plain', $charset = 'utf-8'))->isIdenticalTo($mail)
				->array($mail->getContentType())->isEqualTo(array($type, $charset))
		;
	}

	public function testSend()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->mail = function() {})
			->and($mail = new mailers\mail($adapter))
			->then
				->exception(function() use ($mail) { $mail->send(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('To is undefined')
			->if($mail->addTo($to = uniqid()))
			->then
				->exception(function() use ($mail) { $mail->send(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Subject is undefined')
			->if($mail->setSubject($subject = uniqid()))
			->then
				->exception(function() use ($mail) { $mail->send(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('From is undefined')
			->if($mail->setFrom($from = uniqid()))
			->then
				->exception(function() use ($mail) { $mail->send(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Reply to is undefined')
			->if($mail->setReplyTo($replyTo = uniqid()))
			->then
				->exception(function() use ($mail) { $mail->send(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('X-mailer is undefined')
			->if($mail->setXMailer($mailer = uniqid()))
			->then
				->object($mail->send($message = uniqid()))->isIdenticalTo($mail)
				->adapter($adapter)->call('mail')->withArguments($mail->getTo(), $mail->getSubject(), $message, 'From: ' . $from . "\r\n" . 'Reply-To: ' . $replyTo . "\r\n" .  'X-Mailer: ' . $mailer)->once()
			->assert
				->if($mail->setContentType($type = uniqid(), $charset = uniqid()))
				->then
					->object($mail->send($message = uniqid()))->isIdenticalTo($mail)
					->adapter($adapter)->call('mail')->withArguments($mail->getTo(), $mail->getSubject(), $message, 'From: ' . $from . "\r\n" . 'Reply-To: ' . $replyTo . "\r\n" .  'X-Mailer: ' . $mailer . "\r\n" . 'Content-Type: ' . $type . '; charset="' . $charset . '"')->once();
		;
	}
}
