<?php

/*
Sample atoum configuration file to have report in TAP format.
Do "php path/to/test/file -c path/to/this/file" or "php path/to/atoum/scripts/runner.php -c path/to/this/file -f path/to/test/file" to use it.
*/

use \mageekguy\atoum;

/*
Generate a TAP report.
*/
$tapReport = new atoum\reports\realtime\tap();
$tapReport->addWriter(new atoum\writers\std\out());

$runner->addReport($tapReport);

?>
