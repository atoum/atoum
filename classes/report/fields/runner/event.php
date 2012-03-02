<?php

namespace mageekguy\atoum\report\fields\runner;

use
	mageekguy\atoum\test,
	mageekguy\atoum\runner,
	mageekguy\atoum\locale,
	mageekguy\atoum\report,
	mageekguy\atoum\observable
;

abstract class event extends report\fields\event
{
	public function __construct(locale $locale = null)
	{
		parent::__construct(array(
				runner::runStart,
				test::fail,
				test::error,
				test::uncompleted,
				test::exception,
				test::success,
				runner::runStop
			),
			$locale
		);
	}
}

?>
