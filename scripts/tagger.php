<?php

use
	mageekguy\atoum,
	mageekguy\atoum\scripts
;

require_once __DIR__ . '/../classes/autoloader.php';

$tagger = new scripts\tagger(__FILE__);

set_error_handler(
	function($error, $message, $file, $line) use ($tagger) {
		if (error_reporting() !== 0)
		{
			$tagger->writeError($message);

			exit($error);
		}
	}
);

try
{
	$tagger->run();
}
catch (\exception $exception)
{
	$tagger->writeError($exception->getMessage());

	exit($exception->getCode());
}

exit(0);
