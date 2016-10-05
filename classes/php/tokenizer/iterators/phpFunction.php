<?php

namespace mageekguy\atoum\php\tokenizer\iterators;

use mageekguy\atoum\php\tokenizer;
use mageekguy\atoum\php\tokenizer\iterators;

class phpFunction extends tokenizer\iterator
{
    protected $arguments = [];

    public function getName()
    {
        $name = null;

        $key = $this->findTag(T_FUNCTION);

        if ($key !== null) {
            $this->goToNextTagWhichIsNot([T_WHITESPACE, T_COMMENT]);

            $token = $this->current();

            if ($token !== null && $token->getTag() === T_STRING) {
                $name = $token->getValue();
            }
        }

        return $name;
    }

    public function reset()
    {
        $this->arguments = [];

        return parent::reset();
    }

    public function appendArgument(iterators\phpArgument $phpArgument)
    {
        $this->arguments[] = $phpArgument;

        return $this->append($phpArgument);
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function getArgument($index)
    {
        return (isset($this->arguments[$index]) === false ? null : $this->arguments[$index]);
    }
}
