<?php

namespace atoum\atoum\runner;

use atoum\atoum;
use atoum\atoum\exceptions;

class score extends atoum\score
{
    protected ?string $phpPath = null;
    protected ?string $phpVersion = null;
    protected ?string $atoumPath = null;
    protected ?string $atoumVersion = null;

    public function reset(): static
    {
        $this->phpPath = null;
        $this->phpVersion = null;
        $this->atoumPath = null;
        $this->atoumVersion = null;

        return parent::reset();
    }

    public function setAtoumPath(?string $path): static
    {
        if ($this->atoumPath !== null) {
            throw new exceptions\runtime('Path of atoum is already set');
        }

        $this->atoumPath = $path;

        return $this;
    }

    public function getAtoumPath(): ?string
    {
        return $this->atoumPath;
    }

    public function setAtoumVersion(?string $version): static
    {
        if ($this->atoumVersion !== null) {
            throw new exceptions\runtime('Version of atoum is already set');
        }

        $this->atoumVersion = $version;

        return $this;
    }

    public function getAtoumVersion(): ?string
    {
        return $this->atoumVersion;
    }

    public function setPhpPath(string $path): static
    {
        if ($this->phpPath !== null) {
            throw new exceptions\runtime('PHP path is already set');
        }

        $this->phpPath = (string) $path;

        return $this;
    }

    public function getPhpPath(): ?string
    {
        return $this->phpPath;
    }

    public function setPhpVersion(string $version): static
    {
        if ($this->phpVersion !== null) {
            throw new exceptions\runtime('PHP version is already set');
        }

        $this->phpVersion = trim($version);

        return $this;
    }

    public function getPhpVersion(): ?string
    {
        return $this->phpVersion;
    }
}
