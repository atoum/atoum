<?php

use mageekguy\atoum\scripts;

require_once __DIR__ . '/../classes/autoloader.php';

$treemap = new scripts\treemap(__FILE__);

set_error_handler(function($error, $message, $file, $line) use ($treemap) {
		if (error_reporting() !== 0)
		{
			$treemap->writeError($message);

			exit($error);
		}
	}
);

try
{
	$treemap->run();
}
catch (\exception $exception)
{
	$treemap->writeError($exception->getMessage());

	exit($exception->getCode());
}

exit(0);
