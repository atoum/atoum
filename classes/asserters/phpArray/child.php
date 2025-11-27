<?php

namespace atoum\atoum\asserters\phpArray;

use atoum\atoum\asserters;
use atoum\atoum\exceptions;

class child extends asserters\phpArray
{
    private ?asserters\phpArray $parent = null;

    public function __construct(?asserters\phpArray $parent = null)
    {
        parent::__construct($parent->getGenerator(), $parent->getAnalyzer(), $parent->getLocale());

        $this->setWithParent($parent);
    }

    public function __get(string $property): mixed
    {
        return $this->parentIsSet()->parent->__get($property);
    }

    public function __call(string $method, array $arguments): mixed
    {
        return $this->parentIsSet()->parent->__call($method, $arguments);
    }

    public function __invoke(\Closure $assertions): static
    {
        $assertions($this->parent->phpArray($this->value));

        return $this;
    }

    public function setWithParent(asserters\phpArray $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function hasSize(int $size, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->hasSize($size, $failMessage);

        return $this;
    }

    public function isEmpty(?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->isEmpty($failMessage);

        return $this;
    }

    public function isNotEmpty(?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->isNotEmpty($failMessage);

        return $this;
    }

    public function strictlyContains(mixed $value, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->strictlyContains($value, $failMessage);

        return $this;
    }

    public function contains(mixed $value, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->contains($value, $failMessage);

        return $this;
    }

    public function strictlyNotContains(mixed $value, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->strictlyNotContains($value, $failMessage);

        return $this;
    }

    public function notContains(mixed $value, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->notContains($value, $failMessage);

        return $this;
    }

    public function hasKeys(array $keys, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->hasKeys($keys, $failMessage);

        return $this;
    }

    public function notHasKeys(array $keys, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->notHasKeys($keys, $failMessage);

        return $this;
    }

    public function hasKey(mixed $key, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->hasKey($key, $failMessage);

        return $this;
    }

    public function notHasKey(mixed $key, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->notHasKey($key, $failMessage);

        return $this;
    }

    public function containsValues(array $values, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->containsValues($values, $failMessage);

        return $this;
    }

    public function strictlyContainsValues(array $values, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->strictlyContainsValues($values, $failMessage);

        return $this;
    }

    public function notContainsValues(array $values, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->notContainsValues($values, $failMessage);

        return $this;
    }

    public function strictlyNotContainsValues(array $values, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->strictlyNotContainsValues($values, $failMessage);

        return $this;
    }

    public function isEqualTo(mixed $value, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->isEqualTo($value, $failMessage);

        return $this;
    }

    public function isNotEqualTo(mixed $value, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->isNotEqualTo($value, $failMessage);

        return $this;
    }

    public function isIdenticalTo(mixed $value, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->isIdenticalTo($value, $failMessage);

        return $this;
    }

    public function isNotIdenticalTo(mixed $value, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->isNotIdenticalTo($value, $failMessage);

        return $this;
    }

    public function isReferenceTo(mixed &$reference, ?string $failMessage = null): static
    {
        $this->parentIsSet()->parent->isReferenceTo($reference, $failMessage);

        return $this;
    }

    protected function containsValue(mixed $value, ?string $failMessage, bool $strict): static
    {
        $this->parentIsSet()->parent->containsValue($value, $failMessage, $strict);

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $key): static
    {
        $asserter = new self($this);

        return $asserter->setWith($this->valueIsSet()->value[$key]);
    }

    protected function parentIsSet(): static
    {
        if ($this->parent === null) {
            throw new exceptions\logic('Parent array asserter is undefined');
        }

        return $this;
    }
}
