<?php

namespace atoum\atoum\mock\streams\fs\controller;

use atoum\atoum\mock\streams\fs\controller;

class factory
{
    public function build($name)
    {
        return new controller($name);
    }
}
