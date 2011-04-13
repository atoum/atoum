<?php

namespace mageekguy\atoum\writers;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

class mail extends atoum\writer implements report\writers\asynchronous
{
	protected $mailer = null;

	public function __construct(atoum\adapter $adapter = null, atoum\mailer $mailer = null)
	{
		parent::__construct($adapter);

		if ($mailer === null)
		{
			$mailer = new atoum\mailers\mail();
		}

		$this->setMailer($mailer);
	}

	public function setMailer(atoum\mailer $mailer)
	{
		$this->mailer = $mailer;

		return $this;
	}

	public function getMailer()
	{
		return $this->mailer;
	}

	public function write($something)
	{
		$this->mailer->send($something);

		return $this;
	}

	public function asynchronousWrite($something)
	{
		return $this->write($something);
	}
}

?>
