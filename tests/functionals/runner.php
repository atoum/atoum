<?php

namespace mageekguy\atoum\tests\functionals;

use
	mageekguy\atoum
;

if (defined('atoum\scripts\runner') === false)
{
	define('atoum\scripts\runner', __FILE__);
}

require_once __DIR__ . '/../../scripts/runner.php';

atoum\autoloader::get()
	->addDirectory(__NAMESPACE__ . '\test', __DIR__ . '/test')
;
