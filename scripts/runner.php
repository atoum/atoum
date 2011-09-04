<?php

namespace mageekguy\atoum\scripts\runner;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts
;

require_once(__DIR__ . '/../constants.php');
require_once(atoum\directory . '/classes/autoloader.php');

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);

	scripts\runner::autorun(__FILE__);
}

?>
