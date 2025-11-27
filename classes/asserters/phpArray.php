<?php

namespace atoum\atoum\asserters;

use atoum\atoum\exceptions;

class phpArray extends variable implements \arrayAccess
{
    private mixed $key = null;
    private ?variable $innerAsserter = null;
    private bool $innerAsserterUsed = false;
    private mixed $innerValue = null;
    private bool $innerValueIsSet = false;

    public function __get(string $asserter): mixed
    {
        switch (strtolower($asserter)) {
            case 'keys':
                return $this->getKeysAsserter();

            case 'values':
                return $this->getValuesAsserter();

            case 'size':
                return $this->getSizeAsserter();

            case 'isempty':
                return $this->isEmpty();

            case 'isnotempty':
                return $this->isNotEmpty();

            case 'child':
                $asserter = new phpArray\child($this);

                $this->resetInnerAsserter();

                return $asserter->setWith($this->value);

            default:
                $asserter = parent::__get($asserter);

                if ($asserter instanceof variable === false) {
                    $this->resetInnerAsserter();

                    return $asserter;
                } else {
                    if ($this->innerAsserter === null || $this->innerAsserterUsed === true) {
                        $this->innerValue = $this->value;
                        $this->innerAsserterUsed = false;
                    }

                    $this->innerAsserter = $asserter;

                    return $this;
                }
        }
    }

    public function __call(string $method, array $arguments): mixed
    {
        if ($this->innerAsserterCanUse($method) === false) {
            return parent::__call($method, $arguments);
        } else {
            return $this->callInnerAsserterMethod($method, $arguments);
        }
    }

    public function getKey(): mixed
    {
        return $this->key;
    }

    public function getInnerAsserter(): ?variable
    {
        return $this->innerAsserter;
    }

    public function getInnerValue(): mixed
    {
        return $this->innerValue;
    }

    public function reset(): static
    {
        $this->key = null;

        return parent::reset()->resetInnerAsserter();
    }

    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $key): static
    {
        if ($this->innerAsserter === null) {
            if ($this->analyzer->isArray($this->hasKey($key)->value[$key]) === true) {
                parent::setWith($this->value[$key]);
            } else {
                $this->fail($this->_('Value %s at key %s is not an array', $this->getTypeOf($this->value[$key]), $key));
            }
        } else {
            if (array_key_exists($key, $this->innerValue) === false) {
                $this->fail($this->_('%s has no key %s', $this->getTypeOf($this->innerValue), $this->getTypeOf($key)));
            } else {
                $this->innerValue = $this->innerValue[$key];
                $this->innerValueIsSet = true;
            }
        }

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $key, mixed $value): void
    {
        throw new exceptions\logic('Tested array is read only');
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset(mixed $key): void
    {
        throw new exceptions\logic('Array is read only');
    }

    #[\ReturnTypeWillChange]
    public function offsetExists(mixed $key): bool
    {
        $value = ($this->innerAsserter === null ? $this->value : $this->innerValue);

        return ($value !== null && array_key_exists($key, $value) === true);
    }

    public function setWith(mixed $value, bool $checkType = true): static
    {
        $innerAsserter = $this->innerAsserter;

        if ($innerAsserter !== null) {
            $this->reset();

            $innerAsserter->setWith($value);

            return $this;
        } else {
            parent::setWith($value);

            if ($this->analyzer->isArray($this->value) === true  || $checkType === false) {
                $this->pass();
            } else {
                $this->fail($this->_('%s is not an array', $this));
            }

            return $this;
        }
    }

    public function setByReferenceWith(mixed &$value): static
    {
        if ($this->innerAsserter !== null) {
            return $this->innerAsserter->setByReferenceWith($value);
        } else {
            parent::setByReferenceWith($value);

            if ($this->analyzer->isArray($this->value) === true) {
                $this->pass();
            } else {
                $this->fail($this->_('%s is not an array', $this));
            }

            return $this;
        }
    }

    public function hasSize(int $size, ?string $failMessage = null): static
    {
        if (count($this->valueIsSet()->value) == $size) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s has size %d, expected size %d', $this, count($this->valueIsSet()->value), $size));
        }

        return $this;
    }

    public function isEmpty(?string $failMessage = null): static
    {
        if (count($this->valueIsSet()->value) == 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s is not empty', $this));
        }

        return $this;
    }

    public function isNotEmpty(?string $failMessage = null): static
    {
        if (count($this->valueIsSet()->value) > 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s is empty', $this));
        }

        return $this;
    }

    public function strictlyContains(mixed $value, ?string $failMessage = null): static
    {
        return $this->containsValue($value, $failMessage, true);
    }

    public function contains(mixed $value, ?string $failMessage = null): static
    {
        return $this->containsValue($value, $failMessage, false);
    }

    public function strictlyNotContains(mixed $value, ?string $failMessage = null): static
    {
        return $this->notContainsValue($value, $failMessage, true);
    }

    public function notContains(mixed $value, ?string $failMessage = null): static
    {
        return $this->notContainsValue($value, $failMessage, false);
    }

    public function atKey(mixed $key, ?string $failMessage = null): static
    {
        $this->hasKey($key, $failMessage)->key = $key;

        return $this;
    }

    public function hasKeys(array $keys, ?string $failMessage = null): static
    {
        if (count($undefinedKeys = array_diff($keys, array_keys($this->valueIsSet()->value))) <= 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s has no keys %s', $this, $this->getTypeOf($undefinedKeys)));
        }

        return $this;
    }

    public function notHasKeys(array $keys, ?string $failMessage = null): static
    {
        $this->valueIsSet();

        if (count($definedKeys = array_intersect($keys, array_keys($this->value))) <= 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s has keys %s', $this, $this->getTypeOf($definedKeys)));
        }

        return $this;
    }

    public function hasKey(mixed $key, ?string $failMessage = null): static
    {
        if (array_key_exists($key, $this->valueIsSet()->value)) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s has no key %s', $this, $this->getTypeOf($key)));
        }

        return $this;
    }

    public function notHasKey(mixed $key, ?string $failMessage = null): static
    {
        if (array_key_exists($key, $this->valueIsSet()->value) === false) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s has key %s', $this, $this->getTypeOf($key)));
        }

        return $this;
    }

    public function containsValues(array $values, ?string $failMessage = null): static
    {
        return $this->intersect($values, $failMessage, false);
    }

    public function strictlyContainsValues(array $values, ?string $failMessage = null): static
    {
        return $this->intersect($values, $failMessage, true);
    }

    public function notContainsValues(array $values, ?string $failMessage = null): static
    {
        return $this->notIntersect($values, $failMessage, false);
    }

    public function strictlyNotContainsValues(array $values, ?string $failMessage = null): static
    {
        return $this->notIntersect($values, $failMessage, true);
    }

    public function isEqualTo(mixed $value, ?string $failMessage = null): static
    {
        return $this->callAssertion(__FUNCTION__, [$value, $failMessage]);
    }

    public function isNotEqualTo(mixed $value, ?string $failMessage = null): static
    {
        return $this->callAssertion(__FUNCTION__, [$value, $failMessage]);
    }

    public function isIdenticalTo(mixed $value, ?string $failMessage = null): static
    {
        return $this->callAssertion(__FUNCTION__, [$value, $failMessage]);
    }

    public function isNotIdenticalTo(mixed $value, ?string $failMessage = null): static
    {
        return $this->callAssertion(__FUNCTION__, [$value, $failMessage]);
    }

    public function isReferenceTo(mixed &$reference, ?string $failMessage = null): static
    {
        return $this->callAssertion(__FUNCTION__, [&$reference, $failMessage]);
    }

    protected function containsValue(mixed $value, ?string $failMessage, bool $strict): static
    {
        if (in_array($value, $this->valueIsSet()->value, $strict) === true) {
            if ($this->key === null) {
                $this->pass();
            } else {
                if ($strict === false) {
                    $pass = ($this->value[$this->key] == $value);
                } else {
                    $pass = ($this->value[$this->key] === $value);
                }

                if ($pass === false) {
                    $key = $this->key;
                }

                $this->key = null;

                if ($pass === true) {
                    $this->pass();
                } else {
                    if ($failMessage === null) {
                        if ($strict === false) {
                            $failMessage = $this->_('%s does not contain %s at key %s', $this, $this->getTypeOf($value), $this->getTypeOf($key));
                        } else {
                            $failMessage = $this->_('%s does not strictly contain %s at key %s', $this, $this->getTypeOf($value), $this->getTypeOf($key));
                        }
                    }

                    $this->fail($failMessage);
                }
            }
        } else {
            if ($failMessage === null) {
                if ($strict === false) {
                    $failMessage = $this->_('%s does not contain %s', $this, $this->getTypeOf($value));
                } else {
                    $failMessage = $this->_('%s does not strictly contain %s', $this, $this->getTypeOf($value));
                }
            }

            $this->fail($failMessage);
        }

        return $this;
    }

    protected function notContainsValue(mixed $value, ?string $failMessage, bool $strict): static
    {
        if (in_array($value, $this->valueIsSet()->value, $strict) === false) {
            $this->pass();
        } else {
            if ($this->key === null) {
                if ($failMessage === null) {
                    if ($strict === false) {
                        $failMessage = $this->_('%s contains %s', $this, $this->getTypeOf($value));
                    } else {
                        $failMessage = $this->_('%s strictly contains %s', $this, $this->getTypeOf($value));
                    }
                }

                $this->fail($failMessage);
            } else {
                if ($strict === false) {
                    $pass = ($this->value[$this->key] != $value);
                } else {
                    $pass = ($this->value[$this->key] !== $value);
                }

                if ($pass === false) {
                    $key = $this->key;
                }

                $this->key = null;

                if ($pass === true) {
                    $this->pass();
                } else {
                    if ($failMessage === null) {
                        if ($strict === false) {
                            $failMessage = $this->_('%s contains %s at key %s', $this, $this->getTypeOf($value), $this->getTypeOf($key));
                        } else {
                            $failMessage = $this->_('%s strictly contains %s at key %s', $this, $this->getTypeOf($value), $this->getTypeOf($key));
                        }
                    }

                    $this->fail($failMessage);
                }
            }
        }

        return $this;
    }

    protected function intersect(array $values, ?string $failMessage, bool $strict): static
    {
        $unknownValues = $this->valueIsSet()->getDifference($values, $strict);

        if (count($unknownValues) === 0) {
            $this->pass();
        } else {
            if ($failMessage === null) {
                if ($strict === false) {
                    $failMessage = $this->_('%s does not contain values %s', $this, $this->getTypeOf($unknownValues));
                } else {
                    $failMessage = $this->_('%s does not contain strictly values %s', $this, $this->getTypeOf($unknownValues));
                }
            }

            $this->fail($failMessage);
        }

        return $this;
    }

    protected function notIntersect(array $values, ?string $failMessage, bool $strict): static
    {
        $knownValues = $this->valueIsSet()->getIntersection($values, $strict);

        if (count($knownValues) === 0) {
            $this->pass();
        } else {
            if ($failMessage === null) {
                if ($strict === false) {
                    $failMessage = $this->_('%s contains values %s', $this, $this->getTypeOf($knownValues));
                } else {
                    $failMessage = $this->_('%s contains strictly values %s', $this, $this->getTypeOf($knownValues));
                }
            }

            $this->fail($failMessage);
        }

        return $this;
    }

    protected function getIntersection(array $values, bool $strict): array
    {
        return $this->getValues($values, true, $strict);
    }

    protected function getDifference(array $values, bool $strict): array
    {
        return $this->getValues($values, false, $strict);
    }

    protected function getValues(array $values, bool $equal, bool $strict): array
    {
        return array_values(
            array_filter(
                $values,
                function ($value) use ($strict, $equal) {
                    return in_array($value, $this->value, $strict) === $equal;
                }
            )
        );
    }

    protected function valueIsSet(string $message = 'Array is undefined'): static
    {
        return parent::valueIsSet($message);
    }

    protected function getKeysAsserter(): self
    {
        return $this->generator->__call('phpArray', [array_keys($this->valueIsSet()->value)]);
    }

    protected function getValuesAsserter(): self
    {
        return $this->generator->__call('phpArray', [array_values($this->valueIsSet()->value)]);
    }

    protected function getSizeAsserter(): \atoum\atoum\asserters\integer
    {
        return $this->generator->__call('integer', [count($this->valueIsSet()->value)]);
    }

    protected function callAssertion(string $method, array $arguments): static
    {
        if ($this->innerAsserterCanUse($method) === false) {
            call_user_func_array([parent::class, $method], $arguments);
        } else {
            $this->callInnerAsserterMethod($method, $arguments);
        }

        return $this;
    }

    protected function innerAsserterCanUse(string $method): bool
    {
        return ($this->innerAsserter !== null && $this->innerValueIsSet === true && method_exists($this->innerAsserter, $method) === true);
    }

    protected function callInnerAsserterMethod(string $method, array $arguments): static
    {
        call_user_func_array([$this->innerAsserter->setWith($this->innerValue), $method], $arguments);

        $this->innerAsserterUsed = true;

        return $this;
    }

    protected function resetInnerAsserter(): static
    {
        $this->innerAsserter = null;
        $this->innerValue = null;
        $this->innerValueIsSet = false;

        return $this;
    }
}
