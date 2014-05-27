<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

if (defined('atoum\scripts\runner') === false)
{
	define('atoum\scripts\runner', __FILE__);
}

require_once __DIR__ . '/../../scripts/runner.php';

atoum\autoloader::get()
	->addDirectory(__NAMESPACE__ . '\asserters', __DIR__ . '/asserters')
;
