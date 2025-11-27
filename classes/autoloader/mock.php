<?php

namespace atoum\atoum\autoloader;

use atoum\atoum;
use atoum\atoum\exceptions;

class mock
{
    protected ?atoum\mock\generator $mockGenerator = null;
    protected ?atoum\adapter $adapter = null;

    public function __construct(?atoum\mock\generator $generator = null, ?atoum\adapter $adapter = null)
    {
        $this
            ->setAdapter($adapter)
            ->setMockGenerator($generator)
        ;
    }

    public function setMockGenerator(?atoum\mock\generator $generator = null): static
    {
        $this->mockGenerator = $generator ?: new atoum\mock\generator();

        return $this;
    }

    public function getMockGenerator(): atoum\mock\generator
    {
        return $this->mockGenerator;
    }

    public function setAdapter(?atoum\adapter $adapter = null): static
    {
        $this->adapter = $adapter ?: new atoum\adapter();

        return $this;
    }

    public function getAdapter(): atoum\adapter
    {
        return $this->adapter;
    }

    public function register(): static
    {
        if ($this->adapter->spl_autoload_register([$this, 'requireClass'], true, true) === false) {
            throw new exceptions\runtime('Unable to register mock autoloader');
        }

        return $this;
    }

    public function unregister(): static
    {
        if ($this->adapter->spl_autoload_unregister([$this, 'requireClass']) === false) {
            throw new exceptions\runtime('Unable to unregister mock autoloader');
        }

        return $this;
    }

    public function requireClass(string $class): static
    {
        $mockNamespace = ltrim($this->mockGenerator->getDefaultNamespace(), '\\');
        $mockNamespacePattern = '/^\\\?' . preg_quote($mockNamespace) . '\\\/i';
        $mockedClass = preg_replace($mockNamespacePattern, '', $class);

        if ($mockedClass !== $class) {
            $this->mockGenerator->generate($mockedClass);
        }

        return $this;
    }
}
