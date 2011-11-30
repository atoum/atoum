<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts
;

require_once __DIR__ . '/../classes/autoloader.php';

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);
}

if (autorun === true)
{
	scripts\runner::autorun(__FILE__);
}

?>
