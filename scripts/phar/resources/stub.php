<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts\phar
;

if (extension_loaded('phar') === false)
{
	throw new \runtimeException('Phar extension is mandatory to use this PHAR');
}

define(__NAMESPACE__ . '\phar\name', 'mageekguy.atoum.phar');

\phar::mapPhar(atoum\phar\name);

$versions = unserialize(file_get_contents('phar://' . atoum\phar\name . '/versions'));

require_once 'phar://' . atoum\phar\name . '/' . $versions['current'] . '/classes/autoloader.php';

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);

	phar\stub::autorun(__FILE__);
}

__HALT_COMPILER();
