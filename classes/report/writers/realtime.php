<?php

namespace mageekguy\atoum\report\writers;

use \mageekguy\atoum\reports;

interface realtime
{
	public function realtimeWrite(reports\realtime $report);
}
