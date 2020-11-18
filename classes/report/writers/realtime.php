<?php

namespace atoum\atoum\report\writers;

use atoum\atoum\reports;

interface realtime
{
    public function writeRealtimeReport(reports\realtime $report, $event);
}
