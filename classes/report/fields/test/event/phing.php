<?php

namespace mageekguy\atoum\report\fields\test\event;

use
mageekguy\atoum,
mageekguy\atoum\report,
mageekguy\atoum\exceptions;

class phing extends report\fields\test\event\cli
{
    public function __toString()
    {
        $string = '';

        if ($this->value === atoum\test::runStart) {
            $string = '[';
        }
        else
        {
            if ($this->value === atoum\test::runStop) {
                $string = '] ';
            }
            else
            {
                switch ($this->value)
                {
                    case atoum\test::success:
                        $string = 'S';
                        break;

                    case atoum\test::fail:
                        $string = 'F';
                        break;

                    case atoum\test::error:
                        $string = 'e';
                        break;

                    case atoum\test::exception:
                        $string = 'E';
                        break;
                }
            }
        }

        return $string;
    }
}