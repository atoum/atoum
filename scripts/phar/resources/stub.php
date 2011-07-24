<?php

namespace mageekguy\atoum\scripts\runner;

\phar::mapPhar('mageekguy.atoum.phar');

use
	mageekguy\atoum\scripts\phar
;

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);

	require_once('phar://mageekguy.atoum.phar/classes/autoloader.php');

	phar\stub::autorun(__FILE__);
}

__HALT_COMPILER();
