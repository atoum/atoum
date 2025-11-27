<?php

namespace atoum\atoum\report\fields\runner\php;

use atoum\atoum;
use atoum\atoum\report;
use atoum\atoum\runner;

abstract class version extends report\field
{
    protected ?string $version = null;

    public function __construct()
    {
        parent::__construct([runner::runStart]);
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function handleEvent(string $event, atoum\observable $observable): bool
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->version = $observable->getScore()->getPhpVersion();

            return true;
        }
    }
}
