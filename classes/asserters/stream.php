<?php

namespace atoum\atoum\asserters;

use atoum\atoum;
use atoum\atoum\exceptions;
use atoum\atoum\test;

class stream extends atoum\asserter
{
    protected ?atoum\mock\stream\controller $streamController = null;

    public function __get(string $property): mixed
    {
        switch (strtolower($property)) {
            case 'isread':
            case 'iswritten':
                return $this->{$property}();
            default:
                return parent::__get($property);
        }
    }

    public function setWith(mixed $stream): static
    {
        parent::setWith($stream);

        $this->streamController = atoum\mock\stream::get($stream);

        return $this;
    }

    public function getStreamController(): ?atoum\mock\stream\controller
    {
        return $this->streamController;
    }

    public function isRead(?string $failMessage = null): static
    {
        if (count($this->streamIsSet()->streamController->getCalls(new test\adapter\call('stream_read'))) > 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('stream %s is not read', $this->streamController));
        }

        return $this;
    }

    public function isWritten(?string $failMessage = null): static
    {
        if (count($this->streamIsSet()->streamController->getCalls(new test\adapter\call('stream_write'))) > 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('stream %s is not written', $this->streamController));
        }

        return $this;
    }

    protected function streamIsSet(): static
    {
        if ($this->streamController === null) {
            throw new exceptions\logic('Stream is undefined');
        }

        return $this;
    }
}
