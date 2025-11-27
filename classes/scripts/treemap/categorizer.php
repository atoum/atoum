<?php

namespace atoum\atoum\scripts\treemap;

use atoum\atoum\exceptions;

class categorizer
{
    protected string $name = '';
    protected ?\Closure $callback = null;
    protected string $minDepthColor = '#94ff5a';
    protected string $maxDepthColor = '#00500f';

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->setCallback();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setCallback(?\Closure $callback = null): static
    {
        $this->callback = $callback ?: function () {
            return false;
        };

        return $this;
    }

    public function getCallback(): \Closure
    {
        return $this->callback;
    }

    public function setMinDepthColor(string $color): static
    {
        $this->minDepthColor = static::checkColor($color);

        return $this;
    }

    public function getMinDepthColor(): string
    {
        return $this->minDepthColor;
    }

    public function setMaxDepthColor(string $color): static
    {
        $this->maxDepthColor = static::checkColor($color);

        return $this;
    }

    public function getMaxDepthColor(): string
    {
        return $this->maxDepthColor;
    }

    public function categorize(\splFileInfo $file): bool
    {
        return call_user_func_array($this->callback, [$file]);
    }

    protected static function checkColor(string $color): string
    {
        if (preg_match('/^#?[a-f0-9]{6}$/i', $color) === 0) {
            throw new exceptions\logic\invalidArgument('Color must be in hexadecimal format');
        }

        return '#' . ltrim($color, '#');
    }
}
