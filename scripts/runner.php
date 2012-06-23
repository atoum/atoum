<?php

namespace atoum;

use
	atoum,
	atoum\scripts
;

require_once __DIR__ . '/../classes/autoloader.php';

if (defined(__NAMESPACE__ . '\scripts\runner') === false)
{
	define(__NAMESPACE__ . '\scripts\runner', __FILE__);
}

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);

	scripts\runner::autorun(constant(__NAMESPACE__ . '\scripts\runner'));
}
