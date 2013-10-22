<?php

namespace atoum;

use atoum\scripts;

require_once __DIR__ . '/../classes/autoloader.php';

if (defined(__NAMESPACE__ . '\scripts\runner') === false)
{
	define(__NAMESPACE__ . '\scripts\runner', __FILE__);
}

if (scripts\runner::autorunMustBeEnabled() === true)
{
	scripts\runner::enableAutorun(constant(__NAMESPACE__ . '\scripts\runner'));
}
