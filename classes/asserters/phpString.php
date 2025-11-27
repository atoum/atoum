<?php

namespace atoum\atoum\asserters;

class phpString extends variable
{
    protected ?string $charlist = null;

    public function __get(string $asserter): mixed
    {
        switch (strtolower($asserter)) {
            case 'length':
                return $this->getLengthAsserter();

            case 'isempty':
                return $this->isEmpty();

            case 'isnotempty':
                return $this->isNotEmpty();

            case 'toarray':
                return $this->toArray();

            default:
                return $this->generator->__get($asserter);
        }
    }

    public function __toString(): string
    {
        return (is_string($this->value) === false ? parent::__toString() : $this->_('string(%s) \'%s\'', strlen($this->value), addcslashes($this->value, $this->charlist ?? '')));
    }

    public function getCharlist(): ?string
    {
        return $this->charlist;
    }

    public function setWith(mixed $value, ?string $charlist = null, bool $checkType = true): static
    {
        parent::setWith($value);

        $this->charlist = $charlist;

        if ($checkType === true) {
            if ($this->analyzer->isString($this->value) === true) {
                $this->pass();
            } else {
                $this->fail($this->_('%s is not a string', $this));
            }
        }

        return $this;
    }

    public function isEmpty(?string $failMessage = null): static
    {
        return $this->isEqualTo('', $failMessage ?: $this->_('string is not empty'));
    }

    public function isNotEmpty(?string $failMessage = null): static
    {
        return $this->isNotEqualTo('', $failMessage ?: $this->_('string is empty'));
    }

    public function match(string $pattern, ?string $failMessage = null): static
    {
        return $this->matches($pattern, $failMessage);
    }

    public function matches(string $pattern, ?string $failMessage = null): static
    {
        if (preg_match($pattern, $this->valueIsSet()->value) === 1) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s does not match %s', $this, $pattern));
        }

        return $this;
    }

    public function notMatches(string $pattern, ?string $failMessage = null): static
    {
        if (preg_match($pattern, $this->valueIsSet()->value) === 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s matches %s', $this, $pattern));
        }

        return $this;
    }

    public function isEqualTo(mixed $value, ?string $failMessage = null): static
    {
        return parent::isEqualTo($value, $failMessage ?: $this->_('strings are not equal'));
    }

    public function isEqualToContentsOfFile(string $path, ?string $failMessage = null): static
    {
        $this->valueIsSet();

        $fileContents = @file_get_contents($path);

        if ($fileContents === false) {
            $this->fail($this->_('Unable to get contents of file %s', $path));
        } else {
            return parent::isEqualTo($fileContents, $failMessage ?: $this->_('string is not equal to contents of file %s', $path));
        }
    }

    public function hasLength(int $length, ?string $failMessage = null): static
    {
        if (strlen($this->valueIsSet()->value) == $length) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('length of %s is not %d', $this, $length));
        }

        return $this;
    }

    public function hasLengthGreaterThan(int $length, ?string $failMessage = null): static
    {
        if (strlen($this->valueIsSet()->value) > $length) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('length of %s is not greater than %d', $this, $length));
        }

        return $this;
    }

    public function hasLengthLessThan(int $length, ?string $failMessage = null): static
    {
        if (strlen($this->valueIsSet()->value) < $length) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('length of %s is not less than %d', $this, $length));
        }

        return $this;
    }

    public function contains(string $fragment, ?string $failMessage = null): static
    {
        if (strpos($this->valueIsSet()->value, $fragment) !== false) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s does not contain %s', $this, $fragment));
        }

        return $this;
    }

    public function notContains(string $fragment, ?string $failMessage = null): static
    {
        if (strpos($this->valueIsSet()->value, $fragment) !== false) {
            $this->fail($failMessage ?: $this->_('%s contains %s', $this, $this->analyzer->getTypeOf($fragment)));
        } else {
            $this->pass();
        }

        return $this;
    }

    public function startWith(string $fragment, ?string $failMessage = null): static
    {
        if (strpos($this->valueIsSet()->value, $fragment) === 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s does not start with %s', $this, $this->analyzer->getTypeOf($fragment)));
        }

        return $this;
    }

    public function notStartWith(string $fragment, ?string $failMessage = null): static
    {
        $fragmentPosition = strpos($this->valueIsSet()->value, $fragment);

        if ($fragmentPosition === false || $fragmentPosition > 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s start with %s', $this, $this->analyzer->getTypeOf($fragment)));
        }

        return $this;
    }

    public function endWith(string $fragment, ?string $failMessage = null): static
    {
        if (strpos($this->valueIsSet()->value, $fragment) === (strlen($this->valueIsSet()->value) - strlen($fragment))) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s does not end with %s', $this, $this->analyzer->getTypeOf($fragment)));
        }

        return $this;
    }

    public function notEndWith(string $fragment, ?string $failMessage = null): static
    {
        if (strpos($this->valueIsSet()->value, $fragment) === (strlen($this->valueIsSet()->value) - strlen($fragment))) {
            $this->fail($failMessage ?: $this->_('%s end with %s', $this, $this->analyzer->getTypeOf($fragment)));
        } else {
            $this->pass();
        }

        return $this;
    }

    public function toArray(): phpArray
    {
        return $this->generator->castToArray($this->valueIsSet()->value);
    }

    protected function getLengthAsserter(): \atoum\atoum\asserters\integer
    {
        return $this->generator->__call('integer', [strlen($this->valueIsSet()->value)]);
    }
}
