<?php

namespace atoum\atoum\asserters;

use atoum\atoum;
use atoum\atoum\asserter;
use atoum\atoum\exceptions;

class extension extends asserter
{
    protected ?string $name = null;
    protected ?\Closure $phpExtensionFactory = null;

    public function __construct(?asserter\generator $generator = null, ?atoum\locale $locale = null, ?\Closure $phpExtensionFactory = null)
    {
        parent::__construct($generator, null, $locale);

        $this->setPhpExtensionFactory($phpExtensionFactory);
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function __get(string $asserter): mixed
    {
        switch (strtolower($asserter)) {
            case 'isloaded':
                return $this->{$asserter}();

            default:
                return parent::__get($asserter);
        }
    }

    public function setWith(mixed $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function reset(): static
    {
        $this->name = null;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isLoaded(?string $failMessage = null): static
    {
        $extension = call_user_func($this->phpExtensionFactory, $this->valueIsSet()->name);

        try {
            $extension->requireExtension();

            $this->pass();
        } catch (atoum\php\exception $exception) {
            $this->fail($failMessage ?: $this->_('PHP extension \'%s\' is not loaded', $this));
        }

        return $this;
    }

    protected function valueIsSet(string $message = 'Name of PHP extension is undefined'): static
    {
        if ($this->name === null) {
            throw new exceptions\logic($message);
        }

        return $this;
    }

    protected function pass(): static
    {
        return $this;
    }

    public function setPhpExtensionFactory(?\Closure $factory = null): static
    {
        $this->phpExtensionFactory = $factory ?: function ($extensionName) {
            return new atoum\php\extension($extensionName);
        };

        return $this;
    }
}
