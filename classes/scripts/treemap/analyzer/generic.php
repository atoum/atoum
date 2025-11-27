<?php

namespace atoum\atoum\scripts\treemap\analyzer;

use atoum\atoum\scripts\treemap\analyzer;

class generic implements analyzer
{
    protected string $metricName = '';
    protected string $metricLabel = '';
    protected ?\Closure $callback = null;

    public function __construct(string $metricName, ?string $metricLabel = null, ?\Closure $callback = null)
    {
        $this
            ->setCallback($callback)
            ->setMetricName($metricName)
            ->setMetricLabel($metricLabel)
        ;
    }

    public function setCallback(?\Closure $callback = null): static
    {
        $this->callback = $callback ?: function () {
            return 0;
        };

        return $this;
    }

    public function getCallback(): \Closure
    {
        return $this->callback;
    }

    public function setMetricName(string $metricName): static
    {
        $this->metricName = (string) $metricName;

        return $this->setMetricLabel(ucfirst($this->metricName));
    }

    public function getMetricName(): string
    {
        return $this->metricName;
    }

    public function setMetricLabel(?string $metricLabel = null): static
    {
        $this->metricLabel = ($metricLabel ? (string) $metricLabel : ucfirst($this->metricName));

        return $this;
    }

    public function getMetricLabel(): string
    {
        return $this->metricLabel;
    }

    public function getMetricFromFile(\splFileInfo $file): int|float
    {
        $result = call_user_func_array($this->callback, [$file]);

        return is_numeric($result) ? $result : 0;
    }
}
