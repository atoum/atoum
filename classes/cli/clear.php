<?php

namespace atoum\atoum\cli;

use atoum\atoum;
use atoum\atoum\writer;

class clear implements writer\decorator
{
    protected ?atoum\cli $cli = null;

    public function __construct(?atoum\cli $cli = null)
    {
        $this->setCli($cli);
    }

    public function setCli(?atoum\cli $cli = null): static
    {
        $this->cli = $cli ?: new atoum\cli();

        return $this;
    }

    public function getCli(): atoum\cli
    {
        return $this->cli;
    }

    public function decorate(string $string): string
    {
        return ($this->cli->isTerminal() === false ? PHP_EOL : "\033[1K\r") . $string;
    }
}
