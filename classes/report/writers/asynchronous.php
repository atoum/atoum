<?php

namespace mageekguy\atoum\report\writers;

use \mageekguy\atoum\reports;

interface asynchronous
{
	public function asynchronousWrite(reports\asynchronous $report);
}
