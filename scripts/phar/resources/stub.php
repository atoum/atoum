<?php

namespace mageekguy\atoum\scripts\runner;

\phar::mapPhar('mageekguy.atoum.phar');

use
	mageekguy\atoum,
	mageekguy\atoum\scripts\phar
;

require_once 'phar://mageekguy.atoum.phar/constants.php';
require_once atoum\directory . '/classes/autoloader.php';

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);

	phar\stub::autorun(__FILE__);
}

__HALT_COMPILER();
