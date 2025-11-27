<?php

namespace atoum\atoum\report\fields\runner;

require_once __DIR__ . '/../../../../constants.php';

use atoum\atoum\report;
use atoum\atoum\runner;

abstract class atoum extends report\field
{
    protected mixed $author = null;
    protected mixed $path = null;
    protected mixed $version = null;

    public function __construct()
    {
        parent::__construct([runner::runStart]);
    }

    public function getAuthor(): mixed
    {
        return $this->author;
    }

    public function getVersion(): mixed
    {
        return $this->version;
    }

    public function getPath(): mixed
    {
        return $this->path;
    }

    public function handleEvent(string $event, \atoum\atoum\observable $observable): bool
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->author = \atoum\atoum\author;
            $this->path = $observable->getScore()->getAtoumPath();
            $this->version = $observable->getScore()->getAtoumVersion();

            return true;
        }
    }
}
