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
}

if (autorun === true)
{
	register_shutdown_function(function() {
			$reporter = new atoum\reporters\cli();

			$runner = atoum\runner::getInstance();

			if ($runner->isConfigured() === false)
			{
				$runner
					->configureWith(function(atoum\runner $runner) use ($reporter) { $runner->addObserver($reporter); })
				;
			}

			if ($runner->testIsConfigured() === false)
			{
				$runner
					->configureTestWith(function(atoum\test $test) use ($reporter) { $test->addObserver($reporter); })
				;
			}

			$runner->run();
		}
	);
}

?>
