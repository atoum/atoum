<?php

namespace atoum\atoum\php\tokenizer\iterators;

use atoum\atoum\php\tokenizer;
use atoum\atoum\php\tokenizer\iterators;

class phpArgument extends tokenizer\iterator
{
    protected $defaultValue = null;

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function appendDefaultValue(iterators\phpDefaultValue $phpDefaultValue)
    {
        $this->defaultValue = $phpDefaultValue;

        return $this->append($phpDefaultValue);
    }
}
