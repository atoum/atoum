<?php

namespace atoum\atoum;

use atoum\atoum\tools\variable;

abstract class asserter implements asserter\definition
{
    protected ?locale $locale = null;
    protected ?variable\analyzer $analyzer = null;
    protected ?asserter\generator $generator = null;
    protected ?test $test = null;

    public function __construct(?asserter\generator $generator = null, ?variable\analyzer $analyzer = null, ?locale $locale = null)
    {
        $this
            ->setGenerator($generator)
            ->setAnalyzer($analyzer)
            ->setLocale($locale)
        ;
    }

    public function __get(string $asserter): mixed
    {
        return $this->generator->{$asserter};
    }

    public function __call(string $method, array $arguments): mixed
    {
        switch ($method) {
            case 'foreach':
                if (isset($arguments[0]) === false || (is_array($arguments[0]) === false && $arguments[0] instanceof \traversable === false)) {
                    throw new exceptions\logic\invalidArgument('First argument of ' . get_class($this) . '::' . $method . '() must be an array or a \traversable instance');
                } elseif (isset($arguments[1]) === false || $arguments[1] instanceof \Closure === false) {
                    throw new exceptions\logic\invalidArgument('Second argument of ' . get_class($this) . '::' . $method . '() must be a closure');
                }

                foreach ($arguments[0] as $key => $value) {
                    call_user_func_array($arguments[1], [$this, $value, $key]);
                }

                return $this;

            default:
                return $this->generator->__call($method, $arguments);
        }
    }

    public function reset(): static
    {
        return $this;
    }

    public function setLocale(?locale $locale = null): static
    {
        $this->locale = $locale ?: new locale();

        return $this;
    }

    public function getLocale(): locale
    {
        return $this->locale;
    }

    public function setGenerator(?asserter\generator $generator = null): static
    {
        $this->generator = $generator ?: new asserter\generator();

        return $this;
    }

    public function getGenerator(): asserter\generator
    {
        return $this->generator;
    }

    public function setAnalyzer(?variable\analyzer $analyzer = null): static
    {
        $this->analyzer = $analyzer ?: new variable\analyzer();

        return $this;
    }

    public function getAnalyzer(): variable\analyzer
    {
        return $this->analyzer;
    }

    public function getTest(): ?test
    {
        return $this->test;
    }

    public function setWithTest(test $test): static
    {
        $this->test = $test;

        return $this;
    }

    public function setWith(mixed $mixed): static
    {
        return $this->reset();
    }

    public function setWithArguments(array $arguments): static
    {
        if (count($arguments) > 0) {
            call_user_func_array([$this, 'setWith'], $arguments);
        }

        return $this;
    }

    protected function pass(): static
    {
        if ($this->test !== null) {
            $this->test->getScore()->addPass();
        }

        return $this;
    }

    protected function fail(string $reason): never
    {
        if (is_string($reason) === false) {
            throw new exceptions\logic\invalidArgument('Fail message must be a string');
        }

        throw new asserter\exception($this, $reason);
    }

    protected function getTypeOf(mixed $mixed): string
    {
        return $this->analyzer->getTypeOf($mixed);
    }

    protected function _(string $string): string
    {
        return call_user_func_array([$this->locale, '_'], func_get_args());
    }

    protected function __(string $singular, string $plural, int $quantity): string
    {
        return call_user_func_array([$this->locale, '__'], func_get_args());
    }
}
