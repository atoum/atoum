<?php

namespace atoum\atoum\mock;

use atoum\atoum\mock;

interface aggregator
{
    public function getMockController(): mock\controller;
    public function setMockController(mock\controller $mockController): static;
    public function resetMockController(): static;
    public static function getMockedMethods(): array;
}
