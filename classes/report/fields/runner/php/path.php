<?php

namespace mageekguy\atoum\report\fields\runner\php;

use mageekguy\atoum;
use mageekguy\atoum\report;
use mageekguy\atoum\runner;

abstract class path extends report\field
{
    protected $path = null;

    public function __construct()
    {
        parent::__construct([runner::runStart]);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function handleEvent($event, atoum\observable $observable)
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->path = $observable->getScore()->getPhpPath();

            return true;
        }
    }
}
