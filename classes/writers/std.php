<?php

namespace atoum\atoum\writers;

use atoum\atoum;
use atoum\atoum\report\writers;
use atoum\atoum\reports;

abstract class std extends atoum\writer implements writers\realtime, writers\asynchronous
{
    protected ?atoum\cli $cli = null;
    protected mixed $resource = null;

    public function __construct(?atoum\cli $cli = null, ?atoum\adapter $adapter = null)
    {
        parent::__construct($adapter);

        $this->setCli($cli);
    }

    public function __destruct()
    {
        if ($this->resource !== null) {
            $this->adapter->fclose($this->resource);
        }
    }

    public function setCli(?atoum\cli $cli = null): static
    {
        $this->cli = $cli ?: new atoum\cli();

        return $this;
    }

    public function getCli(): atoum\cli
    {
        return $this->cli;
    }

    public function clear(): static
    {
        $this->doWrite($this->cli->isTerminal() === false ? PHP_EOL : "\033[1K\r");

        return $this;
    }

    public function writeRealtimeReport(reports\realtime $report, string $event): static
    {
        return $this->write((string) $report);
    }

    public function writeAsynchronousReport(reports\asynchronous $report): static
    {
        return $this->write((string) $report);
    }

    protected function doWrite(string $something): void
    {
        $this->init()->adapter->fwrite($this->resource, $something);
    }

    abstract protected function init(): static;
}
