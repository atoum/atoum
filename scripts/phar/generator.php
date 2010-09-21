<?php

namespace mageekguy\atoum\phar;

use \mageekguy\atoum;

require(__DIR__ . '/../../classes/autoloader.php');

$generator = new atoum\phar\generator(__FILE__);
$generator->setOriginDirectory(__DIR__ . '/../..');
$generator->run();

?>
