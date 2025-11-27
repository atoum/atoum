<?php

namespace atoum\atoum\iterators\filters\recursives\atoum;

use atoum\atoum\iterators\filters\recursives;

class source extends recursives\dot
{
    #[\ReturnTypeWillChange]
    public function accept(): bool
    {
        switch ($this->getInnerIterator()->current()->getBasename()) {
            case 'GPATH':
            case 'GRTAGS':
            case 'GTAGS':
            case 'vendor':
            case 'composer.lock':
                return false;

            default:
                return parent::accept();
        }
    }
}
