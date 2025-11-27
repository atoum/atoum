<?php

namespace atoum\atoum\mock\streams\fs\controller;

use atoum\atoum\mock\streams\fs\controller;

class factory
{
    public function build(string $name): controller
    {
        return new controller($name);
    }
}
