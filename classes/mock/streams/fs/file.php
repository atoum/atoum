<?php

namespace atoum\atoum\mock\streams\fs;

use atoum\atoum\mock\stream;

class file extends stream
{
    protected static function getController(string $stream): file\controller
    {
        return new file\controller($stream);
    }
}
