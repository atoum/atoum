<?php

namespace atoum\atoum;

interface observer
{
    public function handleEvent(string $event, observable $observable);
}
