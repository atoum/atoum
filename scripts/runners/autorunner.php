<?php

namespace mageekguy\atoum\runners;

use \mageekguy\atoum;

require_once(__DIR__ . '/../../classes/runner.php');

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);
}

if (constant(__NAMESPACE__ . '\autorun') === true)
{
	if (isset(atoum\registry::getInstance()->currentRunner) === false)
	{
		$report = new atoum\reports\cli();
		$report->addWriter(new atoum\writers\stdout());

		atoum\registry::getInstance()->currentRunner = new atoum\runner();
		atoum\registry::getInstance()->currentRunner->addReport($report);
	}

	if (PHP_SAPI === 'cli' && realpath($_SERVER['argv'][0]) === __FILE__)
	{
		if (defined(__NAMESPACE__ . '\cli') === false)
		{
			define(__NAMESPACE__ . '\cli', true);

			foreach (array_slice($_SERVER['argv'], 1) as $argument)
			{
				switch (true)
				{
					default:
						require_once($argument);
				}
			}

			atoum\registry::getInstance()->currentRunner->run();
		}
	}
	else
	{
		if (defined(__NAMESPACE__ . '\cli') === false)
		{
			define(__NAMESPACE__ . '\cli', false);
		}

		if (cli === false)
		{
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
	}
}

?>
