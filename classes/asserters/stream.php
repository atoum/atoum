<?php

namespace mageekguy\atoum\asserters;

use mageekguy\atoum;
use mageekguy\atoum\exceptions;
use mageekguy\atoum\test;

class stream extends atoum\asserter
{
    protected $streamController = null;

    public function __get($property)
    {
        switch (strtolower($property)) {
            case 'isread':
            case 'iswritten':
                return $this->{$property}();
            default:
                return parent::__get($property);
        }
    }

    public function setWith($stream)
    {
        parent::setWith($stream);

        $this->streamController = atoum\mock\stream::get($stream);

        return $this;
    }

    public function getStreamController()
    {
        return $this->streamController;
    }

    public function isRead($failMessage = null)
    {
        if (count($this->streamIsSet()->streamController->getCalls(new test\adapter\call('stream_read'))) > 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('stream %s is not read', $this->streamController));
        }

        return $this;
    }

    public function isWritten($failMessage = null)
    {
        if (count($this->streamIsSet()->streamController->getCalls(new test\adapter\call('stream_write'))) > 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('stream %s is not written', $this->streamController));
        }

        return $this;
    }

    protected function streamIsSet()
    {
        if ($this->streamController === null) {
            throw new exceptions\logic('Stream is undefined');
        }

        return $this;
    }
}
