<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\scripts
;

require_once __DIR__ . '/../classes/autoloader.php';

autoloader::get()->addDirectory(__NAMESPACE__, __DIR__ . '/../classes');

if (defined(__NAMESPACE__ . '\scripts\runner') === false)
{
	define(__NAMESPACE__ . '\scripts\runner', __FILE__);
}

if (scripts\runner::autorunMustBeEnabled() === true)
{
	scripts\runner::enableAutorun(constant(__NAMESPACE__ . '\scripts\runner'));
}
