<?php

namespace atoum\atoum\asserters;

class sizeOf extends integer
{
    public function setWith(mixed $value): static
    {
        return parent::setWith(count($value));
    }
}
