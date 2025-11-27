<?php

namespace atoum\atoum\report\fields\runner\result\notifier\image;

use atoum\atoum\report\fields\runner\result\notifier\image;

class growl extends image
{
    protected ?string $callbackUrl = null;

    protected function getCommand(): string
    {
        return 'growlnotify --title %s --name atoum --message %s --image %s --url %s';
    }

    public function setCallbackUrl(string $url): static
    {
        $this->callbackUrl = $url;

        return $this;
    }

    public function send(string $title, string $message, mixed $success): string
    {
        return $this->adapter->system(sprintf($this->getCommand(), escapeshellarg($title), escapeshellarg($message), escapeshellarg($this->getImage($success)), escapeshellarg($this->callbackUrl ?? '')));
    }
}
