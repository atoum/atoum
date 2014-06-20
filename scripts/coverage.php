<?php

namespace mageekguy\atoum;

use mageekguy\atoum\scripts;

require_once __DIR__ . '/../classes/autoloader.php';

if (defined(__NAMESPACE__ . '\scripts\coverage') === false)
{
	define(__NAMESPACE__ . '\scripts\coverage', defined('atoum\scripts\coverage') === false ? __FILE__ : \atoum\scripts\coverage);
}

if (scripts\coverage::autorunMustBeEnabled() === true)
{
	scripts\coverage::enableAutorun(constant(__NAMESPACE__ . '\scripts\coverage'));
}
