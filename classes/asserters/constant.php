<?php

namespace atoum\atoum\asserters;

use atoum\atoum;
use atoum\atoum\asserter;
use atoum\atoum\exceptions;
use atoum\atoum\tools;

class constant extends asserter
{
    protected ?tools\diffs\variable $diff = null;
    protected bool $isSet = false;
    protected mixed $value = null;

    public function __construct(?asserter\generator $generator = null, ?tools\variable\analyzer $analyzer = null, ?atoum\locale $locale = null)
    {
        parent::__construct($generator, $analyzer, $locale);

        $this->setDiff();
    }

    public function __toString(): string
    {
        return $this->getTypeOf($this->value);
    }

    public function __call(string $method, array $arguments): mixed
    {
        switch (strtolower($method)) {
            case 'equalto':
                return call_user_func_array([$this, 'isEqualTo'], $arguments);

            default:
                return parent::__call($method, $arguments);
        }
    }

    public function setDiff(?tools\diffs\variable $diff = null): static
    {
        $this->diff = $diff ?: new tools\diffs\variable();

        return $this;
    }

    public function getDiff(): tools\diffs\variable
    {
        return $this->diff;
    }

    public function wasSet(): bool
    {
        return ($this->isSet === true);
    }

    public function setWith(mixed $value): static
    {
        parent::setWith($value);

        $this->value = $value;
        $this->isSet = true;

        return $this;
    }

    public function reset(): static
    {
        $this->value = null;
        $this->isSet = false;

        return parent::reset();
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function isEqualTo(mixed $value, ?string $failMessage = null): static
    {
        if ($this->valueIsSet()->value === $value) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s is not equal to %s', $this, $this->getTypeOf($value)) . PHP_EOL . $this->diff->setExpected($this->value)->setActual($value));
        }

        return $this;
    }

    protected function valueIsSet(string $message = 'Value is undefined'): static
    {
        if ($this->isSet === false) {
            throw new exceptions\logic($message);
        }

        return $this;
    }
}
