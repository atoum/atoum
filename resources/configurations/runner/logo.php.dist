<?php

/*
Sample atoum configuration file to have atoum's logo on tests run.
Do "php path/to/test/file -c path/to/this/file" or "php path/to/atoum/scripts/runner.php -c path/to/this/file -f path/to/test/file" to use it.
*/

use \mageekguy\atoum;

/*
This will add the default CLI report
*/
$cliReport = $script->addDefaultReport();

/*
This will add the atoum logo before each run
*/
$cliReport->addField(new atoum\report\fields\runner\atoum\logo());

/*
This will add a green or red logo after each run depending on its status
*/
$cliReport->addField(new atoum\report\fields\runner\result\logo());

$runner->addReport($cliReport);
