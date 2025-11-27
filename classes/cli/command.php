<?php

namespace atoum\atoum\cli;

use atoum\atoum;

class command
{
    protected ?atoum\adapter $adapter = null;
    protected string $binaryPath = '';
    protected array $options = [];
    protected array $arguments = [];
    protected array $env = [];

    private mixed $processus = null;
    private array $streams = [];
    private string $stdOut = '';
    private string $stdErr = '';
    private ?int $exitCode = null;

    public function __construct(?string $binaryPath = null, ?atoum\adapter $adapter = null)
    {
        $this
            ->setAdapter($adapter)
            ->setBinaryPath($binaryPath)
        ;
    }

    public function __toString(): string
    {
        $command = '';

        foreach ($this->options as $option => $value) {
            $command .= ' ' . $option;

            if ($value !== null) {
                $command .= ' ' . escapeshellarg($value);
            }
        }

        if (count($this->arguments) > 0) {
            $command .= ' --';

            foreach ($this->arguments as $argument) {
                $command .= ' ' . key($argument);

                $value = current($argument);

                if ($value !== null) {
                    $command .= ' ' . escapeshellarg($value);
                }
            }
        }

        if (self::osIsWindows() === true) {
            $command = '"' . $this->binaryPath . '"' . $command;
        } else {
            $command = escapeshellcmd($this->binaryPath) . $command;
        }

        return $command;
    }

    public function __set(string $envVariable, mixed $value): void
    {
        $this->env[$envVariable] = $value;
    }

    public function __get(string $envVariable): mixed
    {
        return (isset($this->{$envVariable}) === false ? null : $this->env[$envVariable]);
    }

    public function __isset(string $envVariable): bool
    {
        return (isset($this->env[$envVariable]) === true);
    }

    public function __unset(string $envVariable): void
    {
        if (isset($this->{$envVariable}) === true) {
            unset($this->env[$envVariable]);
        }
    }

    public function reset(): static
    {
        $this->options = [];
        $this->arguments = [];
        $this->stdOut = '';
        $this->stdErr = '';
        $this->exitCode = null;

        return $this;
    }

    public function getAdapter(): atoum\adapter
    {
        return $this->adapter;
    }

    public function setAdapter(?atoum\adapter $adapter = null): static
    {
        $this->adapter = $adapter ?: new atoum\adapter();

        return $this;
    }

    public function getBinaryPath(): string
    {
        return $this->binaryPath;
    }

    public function setBinaryPath(?string $binaryPath = null): static
    {
        $this->binaryPath = (string) $binaryPath;

        return $this;
    }

    public function addOption(string $option, ?string $value = null): static
    {
        $this->options[$option] = $value ?: null;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function addArgument($argument, $value = null)
    {
        $this->arguments[] = [$argument => $value ?: null];

        return $this;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function isRunning(): bool
    {
        $isRunning = false;

        if ($this->processus !== null) {
            $this->stdOut .= $this->adapter->stream_get_contents($this->streams[1]);
            $this->stdErr .= $this->adapter->stream_get_contents($this->streams[2]);

            $processusStatus = $this->adapter->proc_get_status($this->processus);

            $isRunning = $processusStatus['running'];

            if ($isRunning === false) {
                $this->stdOut .= $this->adapter->stream_get_contents($this->streams[1]);
                $this->adapter->fclose($this->streams[1]);

                $this->stdErr .= $this->adapter->stream_get_contents($this->streams[2]);
                $this->adapter->fclose($this->streams[2]);

                $this->streams = [];

                $this->exitCode = is_int($processusStatus['exitcode']) ? $processusStatus['exitcode'] : (int) $processusStatus['exitcode'];

                $this->adapter->proc_close($this->processus);
                $this->processus = null;
            }
        }

        return $isRunning;
    }

    public function getStdout(): string
    {
        return $this->stdOut;
    }

    public function getStderr(): string
    {
        return $this->stdErr;
    }

    public function getExitCode(): ?int
    {
        while ($this->isRunning() === true);

        return $this->exitCode;
    }

    public function run($stdin = '')
    {
        if ($this->processus !== null) {
            throw new command\exception('Unable to run \'' . $this . '\' because is currently running');
        }

        $pipes = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];

        if ($stdin != '') {
            $pipes[0] = ['pipe', 'r'];
        }

        $this->processus = @call_user_func_array([$this->adapter, 'proc_open'], [(string) $this, $pipes, & $this->streams, null, count($this->env) <= 0 ? null : $this->env]);

        if ($this->processus === false) {
            throw new command\exception('Unable to run \'' . $this . '\'');
        }

        if (isset($this->streams[0]) === true) {
            while ($stdin != '') {
                $stdinWrited = $this->adapter->fwrite($this->streams[0], $stdin, strlen($stdin));

                if ($stdinWrited === false) {
                    throw new command\exception('Unable to send \'' . $stdin . '\' to \'' . $this . '\'');
                }

                $stdin = substr($stdin, $stdinWrited);
            }

            $this->adapter->fclose($this->streams[0]);
            unset($this->streams[0]);
        }

        $this->adapter->stream_set_blocking($this->streams[1], 0);
        $this->adapter->stream_set_blocking($this->streams[2], 0);

        return $this;
    }

    private static function osIsWindows()
    {
        return PHP_OS_FAMILY === 'Windows';
    }
}
