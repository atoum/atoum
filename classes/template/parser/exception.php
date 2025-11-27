<?php

namespace atoum\atoum\template\parser;

use atoum\atoum\exceptions;

class exception extends exceptions\runtime
{
    protected int $errorLine = 0;
    protected int $errorOffset = 0;

    public function __construct(string $message, int $errorLine, int $errorOffset, ?\throwable $previousException = null)
    {
        parent::__construct($message, 0, $previousException);

        $this->errorLine = $errorLine;
        $this->errorOffset = $errorOffset;
    }

    public function getErrorLine(): int
    {
        return $this->errorLine;
    }

    public function getErrorOffset(): int
    {
        return $this->errorOffset;
    }
}
