<?php

namespace atoum\atoum\iterators\filters\recursives;

class closure extends \recursiveFilterIterator
{
    protected array $closures = [];

    public function __construct(\recursiveIterator $iterator, \Closure|array|null $closure = null)
    {
        parent::__construct($iterator);

        if ($closure !== null) {
            foreach ((array) $closure as $c) {
                $this->addClosure($c);
            }
        }
    }

    public function addClosure(\Closure $closure): static
    {
        $this->closures[] = $closure;

        return $this;
    }

    public function getClosures(): array
    {
        return $this->closures;
    }

    #[\ReturnTypeWillChange]
    public function accept(): bool
    {
        foreach ($this->closures as $closure) {
            if ($closure($this->current(), $this->key(), $this->getInnerIterator()) === false) {
                return false;
            }
        }

        return true;
    }

    #[\ReturnTypeWillChange]
    public function getChildren(): static
    {
        return new static(
            $this->getInnerIterator()->getChildren(),
            $this->closures
        );
    }
}
