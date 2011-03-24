<?php

namespace mageekguy\atoum\writers;

use mageekguy\atoum;

class mail extends atoum\writer
{
	protected $to = '';
	protected $from = '';
	protected $mailer = '';
	protected $replyTo = '';
	protected $subject = '';
	protected $message = '';

	public function addTo($to, $realName = null)
	{
		if ($this->to !== '')
		{
			$this->to .= ',';
		}

		if ($realName === null)
		{
			$this->to .= $to;
		}
		else
		{
			$this->to .= $realName . ' <' . $to . '>';
		}

		return $this;
	}

	public function getTo()
	{
		return $this->to;
	}

	public function setSubject($subject)
	{
		$this->subject = (string) $subject;

		return $this;
	}

	public function getSubject()
	{
		return $this->subject;
	}

	public function setMessage($message)
	{
		$this->message = (string) $message;

		return $this;
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function setFrom($from, $realName = null)
	{
		if ($realName === null)
		{
			$this->from = (string) $from;
		}
		else
		{
			$this->from = $realName . ' <' . $from . '>';
		}

		return $this;
	}

	public function getFrom()
	{
		return $this->from;
	}

	public function setReplyTo($replyTo, $realName = null)
	{
		if ($realName === null)
		{
			$this->replyTo = (string) $replyTo;
		}
		else
		{
			$this->replyTo = $realName . ' <' . $replyTo . '>';
		}

		return $this;
	}

	public function getReplyTo()
	{
		return $this->replyTo;
	}

	public function setMailer($mailer)
	{
		$this->mailer = (string) $mailer;

		return $this;
	}

	public function getMailer()
	{
		return $this->mailer;
	}

	public function send()
	{
		return $this->write($this->getMessage());
	}

	public function write($something)
	{
		return $this->flush($something);
	}

	public function flush($something)
	{
		return $this;
	}
}

?>
