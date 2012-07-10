<?php

use
	mageekguy\atoum,
	mageekguy\atoum\scripts
;

require_once __DIR__ . '/../../classes/autoloader.php';

$generator = new scripts\phar\generator(__FILE__);
$generator->setOriginDirectory(__DIR__ . '/../..');
$generator->setStubFile(__DIR__ . '/resources/stub.php');

set_error_handler(function($error, $message, $file, $line) use ($generator) {
		if (error_reporting() !== 0)
		{
			$generator->writeError($message);

			exit($error);
		}
	}
);

try
{
	$generator->run();
}
catch (\exception $exception)
{
	$generator->writeError($exception->getMessage());

	exit($exception->getCode());
}

exit(0);
