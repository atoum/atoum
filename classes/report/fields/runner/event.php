<?php

namespace atoum\report\fields\runner;

use
	atoum\test,
	atoum\runner,
	atoum\locale,
	atoum\report,
	atoum\observable
;

abstract class event extends report\fields\event
{
	public function __construct()
	{
		parent::__construct(array(
				runner::runStart,
				test::fail,
				test::error,
				test::void,
				test::uncompleted,
				test::skipped,
				test::exception,
				test::success,
				runner::runStop
			)
		);
	}
}
