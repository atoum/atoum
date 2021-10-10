<?php

namespace atoum\atoum\extension;

use atoum\atoum\exceptions\logic\invalidArgument;

class aggregator extends \splObjectStorage
{
    #[\ReturnTypeWillChange]
    public function getHash($object)
    {
        if (is_object($object) === false) {
            throw new invalidArgument(__METHOD__ . ' expects parameter 1 to be object, ' . gettype($object) . ' given');
        }

        return get_class($object);
    }
}
