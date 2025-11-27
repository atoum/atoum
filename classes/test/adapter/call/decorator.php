<?php

namespace atoum\atoum\test\adapter\call;

use atoum\atoum\test\adapter\call;

class decorator
{
    protected ?arguments\decorator $argumentsDecorator = null;

    public function __construct()
    {
        $this->setArgumentsDecorator();
    }

    public function getArgumentsDecorator(): arguments\decorator
    {
        return $this->argumentsDecorator;
    }

    public function setArgumentsDecorator(?arguments\decorator $decorator = null): static
    {
        $this->argumentsDecorator = $decorator ?: new arguments\decorator();

        return $this;
    }

    public function decorate(call $call): string
    {
        $string = '';

        $function = $call->getFunction();

        if ($function !== null) {
            $string = $function . '(';

            $arguments = $call->getArguments();

            if ($arguments === null) {
                $string .= '*';
            } else {
                $string .= $this->argumentsDecorator->decorate($call->getArguments());
            }

            $string .= ')';
        }

        return $string;
    }
}
