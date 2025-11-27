<?php

namespace atoum\atoum\factory;

use atoum\atoum\test;

interface builder
{
    public function build(\reflectionClass $class, mixed &$instance = null): mixed;
    public function get(): mixed;
    public function addToAssertionManager(test\assertion\manager $assertionManager, string $factoryName, mixed $defaultHandler): static;
}
