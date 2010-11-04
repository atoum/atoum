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
			try
			{
				$runner = atoum\registry::getInstance()->currentRunner;

				if ($runner->hasObservers() === false)
				{
					$runner->addObserver(new atoum\reporters\cli());

					$stdoutWriter = new atoum\writers\stdout();
					$stringDecorator = new atoum\report\decorators\string();
					$stringDecorator->addWriter($stdoutWriter);

					$report = new atoum\report();
					$report->addRunnerField(new atoum\report\fields\runner\version(), array(atoum\runner::runStart));
					$report->addTestField(new atoum\report\fields\test\run(), array(atoum\test::runStart));
					$report->addTestField(new atoum\report\fields\test\event());
					$report->addTestField(new atoum\report\fields\test\duration(), array(atoum\test::runStop));
					$report->addTestField(new atoum\report\fields\test\memory(), array(atoum\test::runStop));
					$report->addRunnerField(new atoum\report\fields\runner\result(), array(atoum\runner::runStop));
					$report->addRunnerField(new atoum\report\fields\runner\tests\duration(), array(atoum\runner::runStop));
					$report->addRunnerField(new atoum\report\fields\runner\tests\memory(), array(atoum\runner::runStop));
					$report->addRunnerField(new atoum\report\fields\runner\duration(), array(atoum\runner::runStop));
					$report->addRunnerField(new atoum\report\fields\runner\output(), array(atoum\runner::runStop));
					$report->addDecorator($stringDecorator);
					$runner->addObserver($report);
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
