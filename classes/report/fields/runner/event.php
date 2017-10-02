<?php

namespace mageekguy\atoum\report\fields\runner;

use mageekguy\atoum\report;
use mageekguy\atoum\runner;
use mageekguy\atoum\test;

abstract class event extends report\fields\event
{
    public function __construct()
    {
        parent::__construct(
            [
                runner::runStart,
                test::fail,
                test::error,
                test::void,
                test::uncompleted,
                test::skipped,
                test::exception,
                test::success,
                runner::runStop
            ]
        );
    }
}
