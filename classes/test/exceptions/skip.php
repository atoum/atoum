<?php

namespace atoum\atoum\test\exceptions;

use atoum\atoum\exceptions;

class skip extends exceptions\runtime
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
