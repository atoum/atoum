<?php

namespace mageekguy\atoum\reports\realtime;

use mageekguy\atoum\report\fields;
use mageekguy\atoum\reports;

class tap extends reports\realtime
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->addField(new fields\runner\tap\plan())
            ->addField(new fields\test\event\tap())
        ;
    }
}
