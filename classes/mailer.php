<?php

namespace mageekguy\atoum;

abstract class mailer
{
	protected $to = null;
	protected $from = null;
	protected $xMailer = null;
	protected $replyTo = null;
	protected $subject = null;
	protected $contentType = null;
	protected $adapter = null;

	public function __construct(adapter $adapter = null)
	{
		$this->setAdapter($adapter ?: new adapter());
	}

	public function setAdapter(adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

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

	public function setXMailer($mailer)
	{
		$this->xMailer = (string) $mailer;

		return $this;
	}

	public function getXMailer()
	{
		return $this->xMailer;
	}

	public function setContentType($type = 'text/plain', $charset = 'utf-8')
	{
		$this->contentType = array($type, $charset);

		return $this;
	}

	public function getContentType()
	{
		return $this->contentType;
	}

	public abstract function send($something);
}
