<?php

namespace atoum\atoum\reports\realtime;

use atoum\atoum\report\fields;
use atoum\atoum\reports;

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
