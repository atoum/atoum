<?php

namespace mageekguy\atoum\scripts\runner;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);

	if (defined(__NAMESPACE__ . '\class') === false)
	{
		define(__NAMESPACE__ . '\class', '\mageekguy\atoum\scripts\phar\stub');
	}

	require_once('phar://' . __FILE__ . '/classes/autoloader.php');

	register_shutdown_function(function() {
			$class = constant(__NAMESPACE__ . '\class');

			$runner = new $class(__FILE__);

			set_error_handler(function($error, $message, $file, $line) use ($runner) {
					if (error_reporting() !== 0)
					{
						$runner->writeError($message);

						exit($error);
					}
				}
			);

			try
			{
				$runner->run();
			}
			catch (\exception $exception)
			{
				$runner->writeError($exception->getMessage());

				exit($exception->getCode());
			}

			exit(0);
		}
	);
}

__HALT_COMPILER();
