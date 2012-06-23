<?php

namespace mageekguy\atoum\report\fields\test;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\report,
	mageekguy\atoum\test\cli,
	mageekguy\atoum\exceptions
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
