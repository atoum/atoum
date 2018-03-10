<?php

namespace mageekguy\atoum\report\fields\test;

use mageekguy\atoum\report;
use mageekguy\atoum\test;

abstract class event extends report\fields\event
{
    public function __construct()
    {
        parent::__construct(
            [
                test::runStart,
                test::fail,
                test::error,
                test::void,
                test::uncompleted,
                test::skipped,
                test::exception,
                test::runtimeException,
                test::success,
                test::runStop
            ]
        );
    }
}
