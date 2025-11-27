<?php

namespace atoum\atoum\mock\streams\fs\directory;

use atoum\atoum\mock\streams\fs;

class controller extends fs\controller
{
    public function __construct(string $path)
    {
        parent::__construct($path);

        $this->setPermissions('755');
    }

    public function setPermissions($permissions): static
    {
        return parent::setPermissions(0400000 | octdec((string) $permissions));
    }

    public function getContents(): array
    {
        return [];
    }

    public function mkdir(string $path, int $mode, int $options): bool
    {
        if ($this->exists === true) {
            return false;
        } else {
            $this->setPermissions($mode)->exists = true;

            return true;
        }
    }

    public function rmdir(string $path, int $options): bool
    {
        if ($this->exists === false || $this->checkIfWritable() === false) {
            return false;
        } else {
            $this->exists = false;

            return true;
        }
    }

    public function dir_opendir(string $path, bool $useSafeMode): bool
    {
        return $this->exists;
    }

    public function dir_closedir(): bool
    {
        return $this->exists;
    }
}
