<?php

namespace mageekguy\atoum\runners;

use \mageekguy\atoum;
use \mageekguy\atoum\scripts;
use \mageekguy\atoum\exceptions;

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);

	require_once(__DIR__ . '/../classes/runner.php');

	register_shutdown_function(function() {
			$runner = new scripts\runner(__FILE__);

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
