<?php

namespace atoum\atoum\mock\controller;

use atoum\atoum\mock;

class iterator implements \iteratorAggregate
{
    protected ?mock\controller $controller = null;
    protected array $filters = [];

    public function __construct(?mock\controller $controller = null)
    {
        if ($controller != null) {
            $this->setMockController($controller);
        }
    }

    public function __set(string $keyword, mixed $mixed): void
    {
        foreach ($this->getMethods() as $method) {
            $this->controller->{$method}->{$keyword} = $mixed;
        }
    }

    #[\ReturnTypeWillChange]
    public function getIterator(): \arrayIterator
    {
        return new \arrayIterator($this->getMethods());
    }

    public function setMockController(mock\controller $controller): static
    {
        $this->controller = $controller;

        return $this;
    }

    public function getMockController(): ?mock\controller
    {
        return $this->controller;
    }

    public function getMethods(): array
    {
        $methods = ($this->controller === null ? [] : $this->controller->getMethods());

        foreach ($this->filters as $filter) {
            $methods = array_filter($methods, $filter);
        }

        return array_values(array_filter($methods, function ($name) {
            return ($name !== '__construct');
        }));
    }

    public function addFilter(\Closure $filter): static
    {
        $this->filters[] = $filter;

        return $this;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function resetFilters(): static
    {
        $this->filters = [];

        return $this;
    }
}
