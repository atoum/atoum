<?php

namespace atoum\atoum\asserters;

use atoum\atoum\exceptions;

class exception extends phpObject
{
    protected static ?\throwable $lastValue = null;

    public function __get(string $asserter): mixed
    {
        switch (strtolower($asserter)) {
            case 'hasdefaultcode':
            case 'hasnestedexception':
                return $this->{$asserter}();

            case 'message':
                return $this->getMessageAsserter();

            default:
                return parent::__get($asserter);
        }
    }

    public function setWith(mixed $value, bool $checkType = true): static
    {
        $exception = $value;

        if ($exception instanceof \Closure) {
            $exception = null;

            try {
                $value($this->getTest());
            } catch (\throwable $exception) {
            }
        }

        parent::setWith($exception, false);

        if ($checkType === true) {
            if (self::isThrowable($exception) === false) {
                $this->fail($this->_('%s is not an exception', $this));
            } else {
                $this->pass();

                static::$lastValue = $exception;
            }
        }

        return $this;
    }

    public function isInstanceOf(string|object $value, ?string $failMessage = null): static
    {
        try {
            $this->check($value, __FUNCTION__);
        } catch (\logicException $exception) {
            if (self::classExists($value) === false || self::isThrowableClass($value) === false) {
                throw new exceptions\logic\invalidArgument('Argument of ' . __METHOD__ . '() must be a \exception instance or an exception class name');
            }
        }

        return parent::isInstanceOf($value, $failMessage);
    }

    public function hasDefaultCode(?string $failMessage = null): static
    {
        if ($this->valueIsSet()->value->getCode() === 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('code is %s instead of 0', $this->value->getCode()));
        }

        return $this;
    }

    public function hasCode(int|string $code, ?string $failMessage = null): static
    {
        if ($this->valueIsSet()->value->getCode() === $code) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('code is %s instead of %s', $this->value->getCode(), $code));
        }

        return $this;
    }

    public function hasMessage(string $message, ?string $failMessage = null): static
    {
        if ($this->valueIsSet()->value->getMessage() == (string) $message) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('message \'%s\' is not identical to \'%s\'', $this->value->getMessage(), $message));
        }

        return $this;
    }

    public function hasNestedException(?\exception $exception = null, ?string $failMessage = null): static
    {
        $nestedException = $this->valueIsSet()->value->getPrevious();

        if (($exception === null && $nestedException !== null) || ($exception !== null && $nestedException == $exception)) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: ($exception === null ? $this->_('exception does not contain any nested exception') : $this->_('exception does not contain this nested exception')));
        }

        return $this;
    }

    public static function getLastValue(): ?\throwable
    {
        return static::$lastValue;
    }

    protected function valueIsSet(string $message = 'Exception is undefined'): static
    {
        return parent::valueIsSet($message);
    }

    protected function getMessageAsserter(): phpString
    {
        return $this->generator->__call('phpString', [$this->valueIsSet()->value->getMessage()]);
    }

    protected function check(mixed $value, string $method): static
    {
        if (self::isThrowable($value) === false) {
            throw new exceptions\logic\invalidArgument('Argument of ' . __CLASS__ . '::' . $method . '() must be an exception instance');
        }

        return $this;
    }

    private static function isThrowable(mixed $value): bool
    {
        return $value instanceof \throwable;
    }

    private static function isThrowableClass(mixed $value): bool
    {
        return strtolower(ltrim($value, '\\')) === 'throwable' || is_subclass_of($value, 'throwable');
    }
}
