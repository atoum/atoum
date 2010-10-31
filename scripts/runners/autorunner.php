<?php

namespace mageekguy\atoum\runners;

use \mageekguy\atoum;

require_once(__DIR__ . '/../../classes/runner.php');

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);
}

atoum\registry::getInstance()->currentRunner = new atoum\runner();

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
			$runner = atoum\registry::getInstance()->currentRunner;

			if ($runner->hasObservers() === false)
			{
				$runner->addObserver(new atoum\reporters\cli());

				$stdoutWriter = new atoum\writers\stdout();
				$stringDecorator = new atoum\report\decorators\string();
				$stringDecorator->addWriter($stdoutWriter);

				$report = new atoum\report();
				$report->addRunnerField(new atoum\report\fields\runner\version(), array('runnerStart'));
				$report->addRunnerField(new atoum\report\fields\runner\duration(), array('runnerStop'));
				$report->addDecorator($stringDecorator);
				$runner->addObserver($report);
			}

			$runner->run();
		}
	);
}

?>
