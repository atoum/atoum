<?php

namespace atoum\atoum\scripts\treemap\analyzers;

use atoum\atoum\scripts\treemap\analyzer;

class sloc implements analyzer
{
    public function getMetricName(): string
    {
        return 'sloc';
    }

    public function getMetricLabel(): string
    {
        return 'Source Line Of Code';
    }

    public function getMetricFromFile(\splFileInfo $file): int|float
    {
        $codeLines = 0;
        $blankLines = 0;

        foreach ($file->openFile() as $line) {
            if (preg_match('/^\s+$/', $line)) {
                $blankLines++;
            } else {
                $codeLines++;
            }
        }

        $totalLines = $codeLines + $blankLines;

        return $totalLines === 0 ? 0 : ($blankLines / $totalLines <= 0.25 ? $totalLines : $codeLines);
    }
}
