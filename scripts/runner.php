<?php

namespace mageekguy\atoum\scripts\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions;

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);

	if (defined(__NAMESPACE__ . '\class') === false)
	{
		define(__NAMESPACE__ . '\class', '\mageekguy\atoum\scripts\runner');
	}

	require_once(__DIR__ . '/../classes/autoloader.php');

	register_shutdown_function(function() {
			$class = constant(__NAMESPACE__ . '\class');

			$runner = new $class(__FILE__);

			set_error_handler(function($error, $message, $file, $line) use ($runner) {
					$runner->writeError(sprintf($runner->getLocale()->_('Unattended error: %s'), $message));
					exit($error);
				}
			);

			try
			{
				$runner->run();
			}
			catch (exceptions\logic\invalidArgument $exception)
			{
				$runner->writeError($exception->getMessage());
				exit(1);
			}
			catch (\exception $exception)
			{
				$runner->writeError(sprintf($runner->getLocale()->_('Unattended exception: %s'), $exception->getMessage()));
				exit(2);
			}

			exit(0);
		}
	);
}

?>
