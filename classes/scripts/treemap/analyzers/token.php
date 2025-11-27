<?php

namespace atoum\atoum\scripts\treemap\analyzers;

use atoum\atoum\scripts\treemap\analyzer;

class token implements analyzer
{
    public function getMetricName(): string
    {
        return 'token';
    }

    public function getMetricLabel(): string
    {
        return 'PHP tokens';
    }

    public function getMetricFromFile(\splFileInfo $file): int|float
    {
        $tokenFilter = function ($token) {
            if (is_array($token) === true) {
                switch ($token[0]) {
                    case T_WHITESPACE:
                    case T_COMMENT:
                    case T_DOC_COMMENT:
                        return false;

                    default:
                        return true;
                }
            }

            return true;
        };

        return count(array_filter(token_get_all(file_get_contents($file)), $tokenFilter));
    }
}
