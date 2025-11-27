<?php

namespace atoum\atoum\scripts\treemap;

interface analyzer
{
    public function getMetricName(): string;
    public function getMetricLabel(): string;
    public function getMetricFromFile(\splFileInfo $file): int|float;
}
