<?php

namespace mageekguy\atoum\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts\git
;

require_once __DIR__ . '/../../classes/autoloader.php';

$pusher = new git\pusher(__FILE__);

set_error_handler(
	function($error, $message, $file, $line) use ($pusher) {
		if (error_reporting() !== 0)
		{
			$pusher->writeError($message);

			exit($error);
		}
	}
);

try
{
	$pusher->run();
}
catch (\exception $exception)
{
	$pusher->writeError($exception->getMessage());

	exit($exception->getCode());
}

exit(0);
