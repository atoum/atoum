<?php

namespace atoum\atoum\report\fields\test;

use atoum\atoum\report;
use atoum\atoum\test;

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
