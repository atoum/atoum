<?php

namespace mageekguy\atoum;

use mageekguy\atoum\scripts;

require_once __DIR__ . '/../classes/autoloader.php';

if (defined(__NAMESPACE__ . '\scripts\runner') === false)
{
	define(__NAMESPACE__ . '\scripts\runner', defined('atoum\scripts\runner') === false ? __FILE__ : \atoum\scripts\runner);
}

if (scripts\coverage::autorunMustBeEnabled() === true)
{
	scripts\coverage::enableAutorun(constant(__NAMESPACE__ . '\scripts\runner'));
}
