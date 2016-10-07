<?php

namespace mageekguy\atoum\report\fields\test\memory;

use mageekguy\atoum\cli\prompt;
use mageekguy\atoum\locale;
use mageekguy\atoum\report;

class phing extends report\fields\test\memory\cli
{
    public function __toString()
    {
        return $this->prompt .
            sprintf(
                 $this->locale->_('%1$s'),
                 $this->memoryColorizer->colorize($this->value === null ? $this->locale->_('unknown') : $this->locale->_('%4.2f Mb', $this->value / 1048576))
            )
        ;
    }
}
