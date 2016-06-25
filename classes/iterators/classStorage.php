<?php

namespace mageekguy\atoum\iterators;

class classStorage extends \SplObjectStorage
{
    public function getHash($object)
    {
        return get_class($object);
    }
}
