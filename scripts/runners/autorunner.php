<?php

namespace mageekguy\atoum\runners;

use \mageekguy\atoum;

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);

	require_once(__DIR__ . '/../../classes/runner.php');

	if (isset(atoum\registry::getInstance()->currentRunner) === false)
	{
		atoum\registry::getInstance()->currentRunner = new atoum\runner();
	}

	$arguments = new \arrayIterator(array_slice($_SERVER['argv'], 1));

	if (PHP_SAPI === 'cli' && realpath($_SERVER['argv'][0]) === __FILE__)
	{
		foreach ($arguments as $argument)
		{
			require_once($argument);
		}
	}
	else foreach ($arguments as $argument)
	{
		switch ($argument)
		{
			default:
				throw new atoum\exceptions\runtime\unexpectedValue('Argument \'' . $argument . '\' is unknown');
		}
	}

	unset($arguments);

	if (atoum\registry::getInstance()->currentRunner->hasReports() === false)
	{
		$report = new atoum\reports\cli();
		$report->addWriter(new atoum\writers\stdout());

		atoum\registry::getInstance()->currentRunner->addReport($report);

		unset($report);
	}

	register_shutdown_function(function() {
			try
			{
				atoum\registry::getInstance()->currentRunner->run();
			}
			catch (\exception $exception)
			{
				echo $exception->getMessage() . PHP_EOL;
			}
		}
	);
}

?>
