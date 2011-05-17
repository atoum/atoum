<?php

namespace mageekguy\atoum\scripts\runner;

use \mageekguy\atoum\scripts\phar;

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);

	require_once('phar://' . __FILE__ . '/classes/autoloader.php');

	phar\stub::runAtShutdown(__FILE__);
}

__HALT_COMPILER();
