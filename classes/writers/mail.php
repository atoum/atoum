<?php

namespace atoum\atoum\writers;

use atoum\atoum;
use atoum\atoum\report;
use atoum\atoum\reports;

class mail extends atoum\writer implements report\writers\asynchronous
{
    protected ?atoum\mailer $mailer = null;
    protected ?atoum\locale $locale = null;

    public function __construct(?atoum\mailer $mailer = null, ?atoum\locale $locale = null, ?atoum\adapter $adapter = null)
    {
        parent::__construct($adapter);

        $this
            ->setMailer($mailer ?: new atoum\mailers\mail())
            ->setLocale($locale ?: new atoum\locale())
        ;
    }

    public function setMailer(atoum\mailer $mailer): static
    {
        $this->mailer = $mailer;

        return $this;
    }

    public function getMailer(): atoum\mailer
    {
        return $this->mailer;
    }

    public function setLocale(atoum\locale $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale(): atoum\locale
    {
        return $this->locale;
    }

    public function clear(): static
    {
        return $this;
    }

    public function writeAsynchronousReport(reports\asynchronous $report): static
    {
        $mailerSubject = $this->mailer->getSubject();

        if ($mailerSubject === null) {
            $reportTitle = $report->getTitle();

            if ($reportTitle === null) {
                $reportTitle = $this->locale->_('Unit tests report, the %1$s at %2$s', $this->adapter->date($this->locale->_('Y-m-d')), $this->adapter->date($this->locale->_('H:i:s')));
            }

            $this->mailer->setSubject($reportTitle);
        }

        return $this->write((string) $report);
    }

    protected function doWrite(string $something): void
    {
        $this->mailer->send($something);
    }
}
