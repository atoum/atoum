<?php

namespace mageekguy\atoum\runners;

use \mageekguy\atoum;

require_once(__DIR__ . '/../runner.php');

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);
}

function autorun()
{
	$reporter = new atoum\reporters\cli();

	$runner = atoum\runner::getInstance();

	$runner
		->configureRunner(function(atoum\runner $runner) use ($reporter) { $runner->addObserver($reporter); })
		->configureTest(function(atoum\test $test) use ($reporter) { $test->addObserver($reporter); })
		->run()
	;
};

if (PHP_SAPI === 'cli' && realpath($_SERVER['argv'][0]) === __FILE__)
{
	foreach (array_slice($_SERVER['argv'], 1) as $file)
	{
		require($file);
	}

	autorun();
}
else if (autorun === true)
{
	register_shutdown_function(__NAMESPACE__ . '\\autorun');
}

?>
