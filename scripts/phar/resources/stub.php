<?php

namespace mageekguy\atoum;

const phar = 'mageekguy.atoum.phar';

\phar::mapPhar(phar);

use
	mageekguy\atoum,
	mageekguy\atoum\scripts\phar
;

if (defined(__NAMESPACE__ . '\running') === false)
{
	require_once 'phar://' . phar . '/classes/autoloader.php';
}

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);

	phar\stub::autorun(__FILE__);
}

__HALT_COMPILER();
