<?php

namespace atoum\atoum\php\tokenizer\iterators;

use atoum\atoum\php\tokenizer;
use atoum\atoum\php\tokenizer\iterators;

class phpArgument extends tokenizer\iterator
{
    protected ?iterators\phpDefaultValue $defaultValue = null;

    public function getDefaultValue(): ?iterators\phpDefaultValue
    {
        return $this->defaultValue;
    }

    public function appendDefaultValue(iterators\phpDefaultValue $phpDefaultValue): static
    {
        $this->defaultValue = $phpDefaultValue;

        return $this->append($phpDefaultValue);
    }
}
