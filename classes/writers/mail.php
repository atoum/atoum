<?php

namespace mageekguy\atoum\writers;

use
	mageekguy\atoum,
	mageekguy\atoum\report,
	mageekguy\atoum\reports
;

class mail extends atoum\writer implements report\writers\asynchronous
{
	protected $mailer = null;
	protected $locale = null;

	public function __construct(atoum\mailer $mailer = null, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($adapter);

		$this
			->setMailer($mailer ?: new atoum\mailers\mail())
			->setLocale($locale ?: new atoum\locale())
		;
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

	public function setLocale(atoum\locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function clear()
	{
		return $this;
	}

	public function writeAsynchronousReport(reports\asynchronous $report)
	{
		$mailerSubject = $this->mailer->getSubject();

		if ($mailerSubject === null)
		{
			$reportTitle = $report->getTitle();

			if ($reportTitle === null)
			{
				$reportTitle = $this->locale->_('Unit tests report, the %1$s at %2$s', $this->adapter->date($this->locale->_('Y-m-d')), $this->adapter->date($this->locale->_('H:i:s')));
			}

			$this->mailer->setSubject($reportTitle);
		}

		return $this->write((string) $report);
	}

	protected function doWrite($something)
	{
		$this->mailer->send($something);

		return $this;
	}
}
