<?php

namespace mageekguy\atoum\runners;

use \mageekguy\atoum;

require_once(__DIR__ . '/../runner.php');

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['argv'][0]) === __FILE__)
{
	foreach (array_slice($_SERVER['argv'], 1) as $file)
	{
		require($file);
	}

	$runner = new atoum\runner();
	$runner->run();
}
else if (autorun === true)
{
	register_shutdown_function(function()
		{
			$runner = new atoum\runner();
			$runner->run();
		}
	);
}

?>
