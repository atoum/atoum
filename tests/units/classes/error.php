<?php

namespace mageekguy\atoum;

class error
{
	public function doError()
	{
		trigger_error('Message', E_USER_ERROR);
	}
}

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class error extends atoum\test
{
	public function testDoError()
	{
		$error = new \mageekguy\atoum\error();

		$error->doError();

		$this->assert->error('Message', E_USER_ERROR)->exists();
	}
}

?>
