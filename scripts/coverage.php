<?php

use
	mageekguy\atoum,
	mageekguy\atoum\scripts
;

require_once __DIR__ . '/../classes/autoloader.php';

$coverage = new scripts\coverage(__FILE__);

set_error_handler(
	function($error, $message, $file, $line) use ($coverage) {
		if (error_reporting() !== 0)
		{
			$coverage->writeError($message);

			exit($error);
		}
	}
);

try
{
	$coverage->run();
}
catch (\exception $exception)
{
	$coverage->writeError($exception->getMessage());

	exit($exception->getCode());
}

exit(0);
