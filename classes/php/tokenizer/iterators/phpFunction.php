<?php

namespace atoum\atoum\php\tokenizer\iterators;

use atoum\atoum\php\tokenizer;
use atoum\atoum\php\tokenizer\iterators;

class phpFunction extends tokenizer\iterator
{
    protected array $arguments = [];

    public function getName(): ?string
    {
        $name = null;

        $key = $this->findTag(T_FUNCTION);

        if ($key !== null) {
            $this->seek($key);
            $this->goToNextTagWhichIsNot([T_WHITESPACE, T_COMMENT]);

            $token = $this->current();

            if ($token !== null && $token->getTag() === T_STRING) {
                $name = $token->getValue();
            }
        }

        return $name;
    }

    public function reset(): static
    {
        $this->arguments = [];

        return parent::reset();
    }

    public function appendArgument(iterators\phpArgument $phpArgument): static
    {
        $this->arguments[] = $phpArgument;

        return $this->append($phpArgument);
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getArgument(int $index): ?iterators\phpArgument
    {
        return (isset($this->arguments[$index]) === false ? null : $this->arguments[$index]);
    }
}
