<?php

namespace mageekguy\atoum\report\fields\test;

use mageekguy\atoum\report;

abstract class travis extends report\field
{
    public static function slug($name)
    {
        return str_replace('\\', '.', $name);
    }

    public static function time($duration)
    {
        return $duration * pow(10, 9);
    }
}
