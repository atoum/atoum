<?php

namespace atoum\atoum\test\adapter\call\decorators;

use atoum\atoum\test\adapter\call;

class addClass extends call\decorator
{
    protected string $class = '';

    public function __construct(string|object $mixed)
    {
        parent::__construct();

        $this->class = (is_object($mixed) === false ? (string) $mixed : get_class($mixed));
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function decorate(call $call): string
    {
        $string = parent::decorate($call);

        if ($string !== '') {
            $string = $this->class . '::' . $string;
        }

        return $string;
    }
}
