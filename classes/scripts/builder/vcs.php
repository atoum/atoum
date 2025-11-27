<?php

namespace atoum\atoum\scripts\builder;

use atoum\atoum;
use atoum\atoum\exceptions;

abstract class vcs
{
    protected ?atoum\adapter $adapter = null;
    protected ?string $repositoryUrl = null;
    protected int|string|null $revision = null;
    protected ?string $username = null;
    protected ?string $password = null;
    protected ?string $workingDirectory = null;

    public function __construct(?atoum\adapter $adapter = null)
    {
        $this->setAdapter($adapter ?: new atoum\adapter());
    }

    public function setAdapter(atoum\adapter $adapter): static
    {
        $this->adapter = $adapter;

        return $this;
    }

    public function getAdapter(): atoum\adapter
    {
        return $this->adapter;
    }

    public function setWorkingDirectory(string $workingDirectory): static
    {
        $this->workingDirectory = (string) $workingDirectory;

        return $this;
    }

    public function getWorkingDirectory(): ?string
    {
        return $this->workingDirectory;
    }

    public function getWorkingDirectoryIterator(): \recursiveIteratorIterator
    {
        if ($this->workingDirectory === null) {
            throw new exceptions\runtime('Unable to clean working directory because it is undefined');
        }

        return new \recursiveIteratorIterator(new \recursiveDirectoryIterator($this->workingDirectory, \filesystemIterator::KEY_AS_PATHNAME | \filesystemIterator::CURRENT_AS_FILEINFO | \filesystemIterator::SKIP_DOTS), \recursiveIteratorIterator::CHILD_FIRST);
    }

    public function setRepositoryUrl(string $url): static
    {
        $this->repositoryUrl = (string) $url;

        return $this;
    }

    public function getRepositoryUrl(): ?string
    {
        return $this->repositoryUrl;
    }

    public function setRevision($revisionNumber)
    {
        $this->revision = (int) $revisionNumber;

        return $this;
    }

    public function getRevision(): int|string|null
    {
        return $this->revision;
    }

    public function setUsername(string $username): static
    {
        $this->username = (string) $username;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setPassword(string $password): static
    {
        $this->password = (string) $password;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    abstract public function getNextRevisions(): array;

    abstract public function exportRepository(): static;

    public function cleanWorkingDirectory()
    {
        foreach ($this->getWorkingDirectoryIterator() as $inode) {
            if ($inode->isDir() === false) {
                $this->adapter->unlink($inode->getPathname());
            } elseif (($pathname = $inode->getPathname()) !== $this->workingDirectory) {
                $this->adapter->rmdir($pathname);
            }
        }

        return $this;
    }
}
