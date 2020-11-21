<?php

namespace atoum\atoum\mock\streams\fs;

use atoum\atoum\mock\stream;

class directory extends stream
{
    protected static function getController($stream)
    {
        return new directory\controller($stream);
    }
}
