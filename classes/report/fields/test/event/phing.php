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

        if ($this->event === atoum\test::runStart) {
            $string = '[';
        }
        else
        {
            if ($this->event === atoum\test::runStop) {
                $string = '] ';
            }
            else
            {
                switch ($this->event)
                {
                    case atoum\test::success:
                        $string = 'S';
                        break;

                    case atoum\test::uncompleted:
                        $string = 'U';
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