<?php

namespace atoum\atoum;

interface observable
{
    public function callObservers(string $event): void;
    public function getScore(): score;
    public function getBootstrapFile(): ?string;
    public function getAutoloaderFile(): ?string;
}
