<?php

namespace atoum\atoum\report\fields\runner\atoum;

use atoum\atoum;
use atoum\atoum\report;
use atoum\atoum\runner;

abstract class path extends report\field
{
    protected ?string $path = null;

    public function __construct()
    {
        parent::__construct([runner::runStart]);
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function handleEvent(string $event, atoum\observable $observable): bool
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        }

        $this->path = realpath($_SERVER['argv'][0]);

        return true;
    }
}
