<?php

namespace atoum\atoum\scripts\treemap\analyzers;

use atoum\atoum\scripts\treemap\analyzer;

class size implements analyzer
{
    public function getMetricName()
    {
        return 'size';
    }

    public function getMetricLabel()
    {
        return 'Size';
    }

    public function getMetricFromFile(\splFileInfo $file)
    {
        return $file->getSize();
    }
}
