<?php

namespace atoum\atoum\test;

use atoum\atoum;

abstract class engine
{
    abstract public function isAsynchronous(): bool;
    abstract public function run(atoum\test $test): static;
    abstract public function getScore(): ?atoum\score;
}
