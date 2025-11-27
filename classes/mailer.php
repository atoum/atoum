<?php

namespace atoum\atoum;

abstract class mailer
{
    protected ?string $to = null;
    protected ?string $from = null;
    protected ?string $xMailer = null;
    protected ?string $replyTo = null;
    protected ?string $subject = null;
    protected ?array $contentType = null;
    protected ?adapter $adapter = null;

    public function __construct(?adapter $adapter = null)
    {
        $this->setAdapter($adapter ?: new adapter());
    }

    public function setAdapter(adapter $adapter): static
    {
        $this->adapter = $adapter;

        return $this;
    }

    public function getAdapter(): adapter
    {
        return $this->adapter;
    }

    public function addTo(string $to, ?string $realName = null): static
    {
        if ($this->to !== null) {
            $this->to .= ',';
        }

        if ($realName === null) {
            $this->to .= $to;
        } else {
            $this->to .= $realName . ' <' . $to . '>';
        }

        return $this;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = (string) $subject;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setFrom(string $from, ?string $realName = null): static
    {
        if ($realName === null) {
            $this->from = (string) $from;
        } else {
            $this->from = $realName . ' <' . $from . '>';
        }

        return $this;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setReplyTo(string $replyTo, ?string $realName = null): static
    {
        if ($realName === null) {
            $this->replyTo = (string) $replyTo;
        } else {
            $this->replyTo = $realName . ' <' . $replyTo . '>';
        }

        return $this;
    }

    public function getReplyTo(): ?string
    {
        return $this->replyTo;
    }

    public function setXMailer(string $mailer): static
    {
        $this->xMailer = (string) $mailer;

        return $this;
    }

    public function getXMailer(): ?string
    {
        return $this->xMailer;
    }

    public function setContentType(string $type = 'text/plain', string $charset = 'utf-8'): static
    {
        $this->contentType = [$type, $charset];

        return $this;
    }

    public function getContentType(): ?array
    {
        return $this->contentType;
    }

    abstract public function send(string $something): mixed;
}
