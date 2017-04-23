<?php

namespace mageekguy\atoum\factory\builder;

use mageekguy\atoum\factory;
use mageekguy\atoum\test;

class closure implements factory\builder
{
    private $factory = null;
    private $allArgumentsAreOptional = true;

    public function build(\reflectionClass $class, & $instance = null)
    {
        $this->factory = null;

        if ($class->isInterface() === false && $class->isAbstract() === false) {
            $constructor = $class->getConstructor();

            if ($constructor === null || $constructor->isPublic() === true) {
                $constructorParameters = $closureParameters = [];

                if ($constructor !== null) {
                    $this->allArgumentsAreOptional = ($constructor->getNumberOfRequiredParameters() === 0);

                    foreach ($constructor->getParameters() as $position => $parameter) {
                        $closureParameters[$position] = ($parameter->isPassedByReference() === false ? '' : '& ') . $constructorParameters[$position] = '$' . $parameter->getName();

                        if ($parameter->isVariadic()) {
                            $closureParameters[$position] = '...' . $closureParameters[$position];
                            $constructorParameters[$position] = '...' . $constructorParameters[$position];
                        }

                        switch (true) {
                            case $parameter->isDefaultValueAvailable():
                                $defaultValue = var_export($parameter->getDefaultValue(), true);
                                break;

                            case $parameter->isOptional() && $parameter->isVariadic() === false:
                                $defaultValue = 'null';
                                break;

                            default:
                                $defaultValue = null;
                        }

                        if ($defaultValue !== null) {
                            $closureParameters[$position] .= ' = ' . $defaultValue;
                        }
                    }
                }

                if ($constructor === null || count($closureParameters) <= 0) {
                    $this->factory = function (...$arguments) use (& $instance, $class) {
                        return ($instance = $class->newInstanceArgs($arguments));
                    };
                } else {
                    $this->factory = eval('return function(' . implode(', ', $closureParameters) . ') use (& $instance) { return ($instance = new ' . $class->getName() . '(' . implode(', ', $constructorParameters) . ')); };');
                }
            }
        }

        return $this;
    }

    public function get()
    {
        return $this->factory;
    }

    public function addToAssertionManager(test\assertion\manager $assertionManager, $factoryName, $defaultHandler)
    {
        if ($this->factory === null) {
            $assertionManager->setHandler($factoryName, $defaultHandler);
        } else {
            $assertionManager
                ->setMethodHandler($factoryName, $this->factory)
                ->setPropertyHandler($factoryName, $this->allArgumentsAreOptional === false ? $defaultHandler : $this->factory)
            ;
        }

        return $this;
    }
}
