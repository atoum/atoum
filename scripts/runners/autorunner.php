<?php

namespace mageekguy\atoum\runners;

use \mageekguy\atoum;
use \mageekguy\atoum\script;

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);

	require_once(__DIR__ . '/../../classes/runner.php');

	register_shutdown_function(function() {
			try
			{
				if (PHP_SAPI != 'cli')
				{
					throw new atoum\exceptions\runtime('Runner must be used in CLI');
				}

				$runner = new atoum\runner();

				$arguments = new script\arguments\parser();

				$arguments->addHandler(function($script, $argument, $values) use ($runner) {
						if (sizeof($values) <= 0)
						{
							throw new atoum\exceptions\runtime('Argument \'' . $argument . '\' must take at least one argument');
						}

						foreach ($values as $value)
						{
							require_once($value);
						}
					},
					array('-c', '--config-files')
				);

				if (realpath($_SERVER['argv'][0]) === __FILE__)
				{
					$arguments->addHandler(function($script, $argument, $values) {
							foreach ($values as $value)
							{
								require_once($value);
							}
						},
						array('-f', '--files')
					);
				}

				$arguments->parse();

				if ($runner->hasReports() === false)
				{
					$report = new atoum\reports\cli();
					$report->addWriter(new atoum\writers\stdout());

					$runner->addReport($report);
				}

				$runner->run();
			}
			catch (\exception $exception)
			{
				echo $exception->getMessage() . PHP_EOL;
			}
		}
	);
}

?>
