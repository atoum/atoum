<?php

namespace atoum\atoum\report\fields\runner\result\notifier;

use atoum\atoum\report\fields\runner\result\notifier;

class terminal extends notifier
{
    protected ?string $callbackCommand = null;

    public function getCommand(): string
    {
        return 'terminal-notifier -title %s -message %s -execute %s';
    }

    public function setCallbackCommand(string $command): static
    {
        $this->callbackCommand = $command;

        return $this;
    }

    public function send(string $title, string $message, mixed $success): string
    {
        $result = $this->adapter->system(sprintf($this->getCommand(), escapeshellarg($title), escapeshellarg($message), escapeshellarg($this->callbackCommand ?? '')));

        return (string) $result;
    }
}
