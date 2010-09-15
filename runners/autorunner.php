<?php

namespace mageekguy\atoum\runners;

use \mageekguy\atoum;

require_once(__DIR__ . '/../runner.php');

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);
}

$autorun = function() {
	$reporter = new atoum\reporters\cli();

	$runner = new atoum\runner();
	$runner
		->addObserver($reporter)
		->run(function(atoum\test $test) use ($reporter) { $test->addObserver($reporter); })
	;
};

if (PHP_SAPI === 'cli' && realpath($_SERVER['argv'][0]) === __FILE__)
{
	foreach (array_slice($_SERVER['argv'], 1) as $file)
	{
		require($file);
	}

	$autorun();
}
else if (autorun === true)
{
	register_shutdown_function($autorun);
}

?>
