<?php

namespace atoum\report\writers;

use
	atoum\reports
;

interface realtime
{
	public function writeRealtimeReport(reports\realtime $report, $event);
}
