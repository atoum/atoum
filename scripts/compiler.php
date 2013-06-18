<?php

use
	mageekguy\atoum,
	mageekguy\atoum\scripts
;

require_once __DIR__ . '/../classes/autoloader.php';

$compiler = new scripts\compiler(__FILE__);

set_error_handler(function($error, $message, $file, $line) use ($compiler) {
		if (error_reporting() !== 0)
		{
			$compiler->writeError($message);

			exit($error);
		}
	}
);

try
{
	$compiler->run();
}
catch (\exception $exception)
{
	$compiler->writeError($exception->getMessage());

	exit($exception->getCode());
}

exit(0);
