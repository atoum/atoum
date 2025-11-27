<?php

namespace atoum\atoum\writer\decorators;

use atoum\atoum\writer;

class prompt implements writer\decorator
{
    public const defaultPrompt = '$ ';

    protected string $prompt = '';

    public function __construct(?string $prompt = null)
    {
        $this->setPrompt($prompt);
    }

    public function setPrompt(?string $prompt = null): static
    {
        $this->prompt = $prompt ?: static::defaultPrompt;

        return $this;
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function decorate(string $message): string
    {
        return $this->prompt . $message;
    }
}
