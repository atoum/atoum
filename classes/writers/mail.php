<?php

namespace mageekguy\atoum\writers;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report,
	\mageekguy\atoum\reports
;

class mail extends atoum\writer implements report\writers\asynchronous
{
	protected $mailer = null;

	public function __construct(atoum\mailer $mailer = null, atoum\adapter $adapter = null)
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

	public function writeAsynchronousReport(reports\asynchronous $report)
	{
		$mailerSubject = $this->mailer->getSubject();

		if ($mailerSubject === null)
		{
			$reportTitle = $report->getTitle();

			if ($reportTitle !== null)
			{
				$this->mailer->setSubject($reportTitle);
			}
		}

		return $this->write((string) $report);
	}
}

?>
