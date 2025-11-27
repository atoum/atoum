<?php

namespace atoum\atoum\cli\commands;

use atoum\atoum\cli;

class git
{
    public const defaultPath = 'git';

    protected ?cli\command $command = null;

    public function __construct(?string $path = null)
    {
        $this
            ->setCommand()
            ->setPath($path)
        ;
    }

    public function setPath(?string $path = null): static
    {
        $this->command->setBinaryPath($path ?: static::defaultPath);

        return $this;
    }

    public function getPath(): string
    {
        return $this->command->getBinaryPath();
    }

    public function setCommand(?cli\command $command = null): static
    {
        $this->command = $command ?: new cli\command();

        return $this;
    }

    public function getCommand(): cli\command
    {
        return $this->command;
    }

    public function addAllAndCommit(string $message): static
    {
        $this->command
            ->reset()
            ->addOption('commit -am \'' . addslashes($message) . '\'')
        ;

        return $this->run();
    }

    public function resetHardTo(string $commit): static
    {
        $this->command
            ->reset()
            ->addOption('reset --hard ' . $commit)
        ;

        return $this->run();
    }

    public function createTag(string $tag): static
    {
        $this->command
            ->reset()
            ->addOption('tag ' . $tag)
        ;

        return $this->run();
    }

    public function deleteLocalTag(string $tag): static
    {
        $this->command
            ->reset()
            ->addOption('tag -d ' . $tag)
        ;

        return $this->run();
    }

    public function push(?string $remote = null, ?string $branch = null): static
    {
        $this->command
            ->reset()
            ->addOption('push ' . ($remote ?: 'origin') . ' ' . ($branch ?: $this->getCurrentBranch()))
        ;

        return $this->run();
    }

    public function forcePush($remote = null, $branch = null)
    {
        $this->command
            ->reset()
            ->addOption('push --force ' . ($remote ?: 'origin') . ' ' . ($branch ?: $this->getCurrentBranch()))
        ;

        return $this->run();
    }

    public function pushTag($tag, $remote = null)
    {
        $this->command
            ->reset()
            ->addOption('push ' . ($remote ?: 'origin') . ' ' . $tag)
        ;

        return $this->run();
    }

    public function checkoutAllFiles()
    {
        $this->command
            ->reset()
            ->addOption('checkout .')
        ;

        return $this->run();
    }

    protected function run(): static
    {
        if ($this->command->run()->getExitCode() !== 0) {
            throw new cli\command\exception('Unable to execute \'' . $this->command . '\': ' . $this->command->getStderr());
        }

        return $this;
    }

    public function getCurrentBranch(): string
    {
        $this->command
            ->reset()
            ->addOption('rev-parse --abbrev-ref HEAD')
        ;

        $branch = trim($this->run()->command->getStdout()) ?: 'master';

        $this->command->reset();

        return $branch;
    }
}
