<?php

namespace mageekguy\atoum\test\adapter;

use mageekguy\atoum\asserter\exception;
use mageekguy\atoum\exceptions;
use mageekguy\atoum\test\adapter;

class call
{
    protected $function = null;
    protected $arguments = null;
    protected $decorator = null;
    protected $verify = null;

    public function __construct($function = null, array $arguments = null, adapter\call\decorator $decorator = null)
    {
        if ($function !== null) {
            $this->setFunction($function);
        }

        $this->arguments = $arguments;

        $this->setDecorator($decorator);
    }

    public function __toString()
    {
        return $this->decorator->decorate($this);
    }

    public function getFunction()
    {
        return $this->function;
    }

    public function setFunction($function)
    {
        $function = (string) $function;

        if ($function === '') {
            throw new exceptions\logic\invalidArgument('Function must not be empty');
        }

        $this->function = $function;

        return $this;
    }

    public function copy(self $call)
    {
        $this->function = $call->function;
        $this->arguments = $call->arguments;
        $this->decorator = $call->decorator;

        return $this;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function unsetArguments()
    {
        $this->arguments = null;

        return $this;
    }

    public function getVerify()
    {
        return $this->verify;
    }

    public function setVerify(callable $verify)
    {
        $this->verify = $verify;

        return $this;
    }

    public function unsetVerify()
    {
        $this->verify = null;

        return $this;
    }

    public function setDecorator(adapter\call\decorator $decorator = null)
    {
        $this->decorator = $decorator ?: new adapter\call\decorator();

        return $this;
    }

    public function getDecorator()
    {
        return $this->decorator;
    }

    public function isEqualTo(self $call)
    {
        switch (true) {
            case $this->function === null || strtolower($this->function) != strtolower($call->function):
                return false;

            case $this->verify !== null:
                try {
                    $result = call_user_func($this->verify, $call->arguments);
                    $result = $result === null ? true : $result;

                    $this->verify = function () use ($result) {
                        return $result;
                    };

                    return $result;
                } catch (exception $e) {
                    $this->verify = function () {
                        return false;
                    };

                    return false;
                }

            case $this->arguments === null:
                return true;

            case $call->arguments === null:
                return false;

            case count($this->arguments) <= 0:
                return $this->arguments == $call->arguments;

            case count($this->arguments) <= count($call->arguments):
                $callback = function ($a, $b) {
                    return ($a == $b ? 0 : -1);
                };

                return (count($this->arguments) == count(array_uintersect_uassoc($call->arguments, $this->arguments, $callback, $callback)));

            default:
                return false;
        }
    }

    public function isIdenticalTo(self $call)
    {
        $isIdentical = $this->isEqualTo($call);

        if ($isIdentical === true && $this->arguments !== null && $call->arguments !== null) {
            $callback = function ($a, $b) {
                return ($a === $b ? 0 : -1);
            };

            $isIdentical = ($this->arguments === array_uintersect_uassoc($call->arguments, $this->arguments, $callback, $callback));
        }

        return $isIdentical;
    }

    public function isFullyQualified()
    {
        return ($this->function !== null && $this->arguments !== null);
    }
}
