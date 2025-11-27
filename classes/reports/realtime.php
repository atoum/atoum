<?php

namespace atoum\atoum\reports;

use atoum\atoum;
use atoum\atoum\report;

abstract class realtime extends atoum\report
{
    public function handleEvent(string $event, atoum\observable $observable): void
    {
        parent::handleEvent($event, $observable);
        $this->write($event);

        if ($event === atoum\runner::runStop) {
            foreach ($this->writers as $writer) {
                $writer->reset();
            }
        }
    }

    public function addWriter(report\writers\realtime $writer): static
    {
        return $this->doAddWriter($writer);
    }

    public function isOverridableBy(report $report): bool
    {
        return ($report instanceof self) === false;
    }

    protected function write(string $event): static
    {
        foreach ($this->writers as $writer) {
            $writer->writeRealtimeReport($this, $event);
        }

        return $this;
    }
}
