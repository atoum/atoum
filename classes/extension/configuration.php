<?php

namespace atoum\atoum\extension;

interface configuration
{
    public function serialize();

    public static function unserialize(array $configuration);
}
