<?php

namespace mageekguy\atoum\writers;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class mail extends atoum\writer
{
	protected $to = null;
	protected $from = null;
	protected $mailer = null;
	protected $replyTo = null;
	protected $subject = null;

	public function addTo($to, $realName = null)
	{
		if ($this->to !== null)
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

	public function write($something)
	{
		return $this->flush($something);
	}

	public function flush($something)
	{
		if ($this->to === null)
		{
			throw new exceptions\runtime('To is undefined');
		}

		if ($this->subject === null)
		{
			throw new exceptions\runtime('Subject is undefined');
		}

		if ($this->from === null)
		{
			throw new exceptions\runtime('From is undefined');
		}

		if ($this->replyTo === null)
		{
			throw new exceptions\runtime('Reply to is undefined');
		}

		if ($this->mailer === null)
		{
			throw new exceptions\runtime('Mailer is undefined');
		}

		$this->adapter->mail($this->to, $this->subject, (string) $something, 'From: ' . $this->from . "\r\n" . 'Reply-To: ' . $this->replyTo . "\r\n" . 'X-Mailer: ' . $this->mailer);

		return $this;
	}
}

?>
