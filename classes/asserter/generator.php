<?php

namespace atoum\atoum\asserter;

use atoum\atoum;
use atoum\atoum\asserter;
use atoum\atoum\exceptions;

class generator
{
    protected ?atoum\locale $locale = null;
    protected ?asserter\resolver $resolver = null;

    public function __construct(?atoum\locale $locale = null, ?asserter\resolver $resolver = null)
    {
        $this
            ->setLocale($locale)
            ->setResolver($resolver)
        ;
    }

    public function __get(string $property): mixed
    {
        return $this->getAsserterInstance($property);
    }

    public function __isset(string $property): bool
    {
        return ($this->getAsserterClass($property) !== null);
    }

    public function __call(string $method, array $arguments): mixed
    {
        return $this->getAsserterInstance($method, $arguments);
    }

    public function setBaseClass(string $baseClass): static
    {
        $this->resolver->setBaseClass($baseClass);

        return $this;
    }

    public function getBaseClass(): string
    {
        return $this->resolver->getBaseClass();
    }

    public function addNamespace(string $namespace): static
    {
        $this->resolver->addNamespace($namespace);

        return $this;
    }

    public function getNamespaces(): array
    {
        return $this->resolver->getNamespaces();
    }

    public function setLocale(?atoum\locale $locale = null): static
    {
        $this->locale = $locale ?: new atoum\locale();

        return $this;
    }

    public function getLocale(): atoum\locale
    {
        return $this->locale;
    }

    public function setResolver(?asserter\resolver $resolver = null): static
    {
        $this->resolver = $resolver ?: new asserter\resolver();

        return $this;
    }

    public function getResolver(): asserter\resolver
    {
        return $this->resolver;
    }

    public function getAsserterClass(string $asserter): ?string
    {
        return $this->resolver->resolve($asserter);
    }

    public function getAsserterInstance(string $asserter, array $arguments = [], ?atoum\test $test = null): atoum\asserter
    {
        if (($asserterClass = $this->getAsserterClass($asserter)) === null) {
            throw new exceptions\logic\invalidArgument('Asserter \'' . $asserter . '\' does not exist');
        }

        $asserterInstance = new $asserterClass();

        if ($test !== null) {
            $asserterInstance->setWithTest($test);
        }

        return $asserterInstance
            ->setGenerator($this)
            ->setLocale($this->locale)
            ->setWithArguments($arguments)
        ;
    }
}
