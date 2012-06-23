<?php

namespace atoum\report\fields\test;

use
	atoum,
	atoum\test,
	atoum\report,
	atoum\test\cli,
	atoum\exceptions
;

abstract class event extends report\fields\event
{
	public function __construct(atoum\locale $locale = null)
	{
		parent::__construct(array(
				test::runStart,
				test::fail,
				test::error,
				test::uncompleted,
				test::exception,
				test::runtimeException,
				test::success,
				test::runStop
			),
			$locale
		);
	}
}
