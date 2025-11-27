<?php

namespace atoum\atoum\fs\path;

use atoum\atoum;
use atoum\atoum\fs\path;

class factory
{
    protected ?atoum\adapter $adapter = null;
    protected ?string $directorySeparator = null;

    public function setDirectorySeparator(?string $directorySeparator = null): static
    {
        $this->directorySeparator = $directorySeparator;

        return $this;
    }

    public function setAdapter(?atoum\adapter $adapter = null): static
    {
        $this->adapter = $adapter;

        return $this;
    }

    public function build(string $path): path
    {
        return new path($path, $this->directorySeparator, $this->adapter);
    }
}
