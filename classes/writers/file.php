<?php

namespace atoum\atoum\writers;

use atoum\atoum;
use atoum\atoum\exceptions;
use atoum\atoum\report\writers;
use atoum\atoum\reports;

class file extends atoum\writer implements writers\realtime, writers\asynchronous
{
    protected ?string $filename = null;

    private mixed $resource = null;

    public const defaultFileName = 'atoum.log';

    public function __construct(?string $filename = null, ?atoum\adapter $adapter = null)
    {
        parent::__construct($adapter);

        $this->setFilename($filename);
    }

    public function __destruct()
    {
        $this->closeFile();
    }

    public function clear(): static
    {
        if ($this->openFile()->adapter->ftruncate($this->resource, 0) === false) {
            throw new exceptions\runtime('Unable to truncate file \'' . $this->filename . '\'');
        }

        return $this;
    }

    public function writeRealtimeReport(reports\realtime $report, string $event): static
    {
        return $this->write((string) $report);
    }

    public function writeAsynchronousReport(reports\asynchronous $report): static
    {
        return $this->write((string) $report)->closeFile();
    }

    public function setFilename(?string $filename = null): static
    {
        $this->closeFile()->filename = $filename ?: self::defaultFileName;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function reset(): static
    {
        $this->closeFile();

        return parent::reset();
    }

    protected function doWrite(string $something): void
    {
        if (strlen($something) != $this->openFile()->adapter->fwrite($this->resource, $something)) {
            throw new exceptions\runtime('Unable to write in file \'' . $this->filename . '\'');
        }

        $this->adapter->fflush($this->resource);
    }

    private function openFile(): static
    {
        if ($this->resource === null) {
            $this->resource = @$this->adapter->fopen($this->filename, 'c') ?: null;

            if ($this->resource === null) {
                throw new exceptions\runtime('Unable to open file \'' . $this->filename . '\'');
            }

            if ($this->adapter->flock($this->resource, LOCK_EX) === false) {
                throw new exceptions\runtime('Unable to lock file \'' . $this->filename . '\'');
            }

            $this->clear();
        }

        return $this;
    }

    private function closeFile(): static
    {
        if ($this->resource !== null) {
            $this->adapter->flock($this->resource, LOCK_UN);
            $this->adapter->fclose($this->resource);

            $this->resource = null;
        }

        return $this;
    }
}
