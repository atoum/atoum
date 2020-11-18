<?php

namespace atoum\atoum;

interface observer
{
    public function handleEvent($event, observable $observable);
}
