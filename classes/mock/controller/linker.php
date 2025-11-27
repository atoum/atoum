<?php

namespace atoum\atoum\mock\controller;

use atoum\atoum\mock;

class linker
{
    protected ?\splObjectStorage $mocks = null;
    protected ?\splObjectStorage $controllers = null;

    public function __construct()
    {
        $this->init();
    }

    public function link(mock\controller $controller, mock\aggregator $mock): static
    {
        $currentMock = $this->getMock($controller);

        if ($currentMock === null || $currentMock !== $this) {
            if ($currentMock !== $this) {
                $this->unlink($controller);
            }

            $this->mocks[$controller] = $mock;
            $this->controllers[$mock] = $controller;

            $controller->control($mock);
        }

        return $this;
    }

    public function getController(mock\aggregator $mock): ?mock\controller
    {
        return (isset($this->controllers[$mock]) === false ? null : $this->controllers[$mock]);
    }

    public function getMock(mock\controller $controller): ?mock\aggregator
    {
        return (isset($this->mocks[$controller]) === false ? null : $this->mocks[$controller]);
    }

    public function unlink(mock\controller $controller): static
    {
        $mock = $this->getMock($controller);

        if ($mock !== null) {
            unset($this->controllers[$mock]);
            unset($this->mocks[$controller]);

            $controller->reset();
        }

        return $this;
    }

    public function reset(): static
    {
        foreach ($this->mocks as $controller) {
            $controller->reset();
        }

        return $this->init();
    }

    protected function init(): static
    {
        $this->mocks = new \splObjectStorage();
        $this->controllers = new \splObjectStorage();

        return $this;
    }
}
