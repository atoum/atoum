<?php

namespace mageekguy\atoum\scripts\runner;

use \mageekguy\atoum\scripts;

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);

	require_once(__DIR__ . '/../classes/autoloader.php');

	scripts\runner::runAtShutdown(__FILE__);
}

?>
