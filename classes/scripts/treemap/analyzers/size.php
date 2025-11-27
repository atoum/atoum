<?php

namespace atoum\atoum\scripts\treemap\analyzers;

use atoum\atoum\scripts\treemap\analyzer;

class size implements analyzer
{
    public function getMetricName(): string
    {
        return 'size';
    }

    public function getMetricLabel(): string
    {
        return 'Size';
    }

    public function getMetricFromFile(\splFileInfo $file): int|float
    {
        return $file->getSize();
    }
}
