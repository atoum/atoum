<?php

namespace atoum\atoum\test\assertion;

use atoum\atoum\asserter;

class aliaser implements \arrayAccess
{
    protected ?asserter\resolver $resolver = null;
    protected array $aliases = [];

    private mixed $context = null;
    private ?string $keyword = null;

    public function __construct(?asserter\resolver $resolver = null)
    {
        $this->setResolver($resolver);
    }

    public function __set(string $alias, string $keyword): void
    {
        $this->aliasKeyword($keyword, $alias);
    }

    public function __get(string $alias): ?string
    {
        return $this->resolveAlias($alias);
    }

    public function __unset(string $alias): void
    {
        $contextKey = $this->getContextKey($this->context);

        if (isset($this->aliases[$contextKey]) === true) {
            $aliasKey = $this->getAliasKey($alias);

            if (isset($this->aliases[$contextKey][$aliasKey]) === true) {
                unset($this->aliases[$contextKey][$aliasKey]);
            }
        }
    }

    public function __isset(string $alias): bool
    {
        $contextKey = $this->getContextKey($this->context);

        if (isset($this->aliases[$contextKey]) === true) {
            $aliasKey = $this->getAliasKey($alias);

            return (isset($this->aliases[$contextKey][$aliasKey]) === true);
        }

        return false;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($context)
    {
        $this->context = $context;

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($newContext, $context)
    {
        $contextKey = $this->getContextKey($context);

        if (isset($this->aliases[$contextKey]) === true) {
            $this->aliases[$this->getContextKey($newContext)] = $this->aliases[$contextKey];
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($context)
    {
        $contextKey = $this->getContextKey($context);

        if (isset($this->aliases[$contextKey]) === true) {
            unset($this->aliases[$contextKey]);
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($context)
    {
        return (isset($this->aliases[$this->getContextKey($context)]) === true);
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

    public function from($context)
    {
        $this->context = $context;

        return $this;
    }

    public function alias($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function to($alias)
    {
        $this->aliasKeyword($this->keyword, $alias, $this->context);

        $this->keyword = null;

        return $this;
    }

    public function aliasKeyword(string $keyword, string $alias, mixed $context = null): static
    {
        $this->aliases[$this->getContextKey($context)][$this->getAliasKey($alias)] = $keyword;

        return $this;
    }

    public function resolveAlias(string $alias, mixed $context = null): ?string
    {
        $aliasKey = $this->getAliasKey($alias);
        $contextKey = $this->getContextKey($context);

        return (isset($this->aliases[$contextKey]) === false || isset($this->aliases[$contextKey][$aliasKey]) === false ? $alias : $this->aliases[$contextKey][$aliasKey]);
    }

    private function getAliasKey($alias)
    {
        return strtolower($alias);
    }

    private function getContextKey($context)
    {
        if ($context === null && $this->context !== null) {
            $context = $this->context;

            $this->context = null;
        }

        return ($context == '' ? '' : strtolower($this->resolver->resolve($context) ?: $context));
    }
}
