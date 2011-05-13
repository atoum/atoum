#!/usr/bin/env php
<?php

namespace mageekguy\atoum\phar;

use \mageekguy\atoum\scripts\phar;

require_once('phar://' . __FILE__ . '/classes/autoloader.php');

$stub = new phar\stub(__FILE__);

set_error_handler(function($error, $message, $file, $line) use ($stub) {
		if (error_reporting() !== 0)
		{
			$stub->writeError($message);

			exit($error);
		}
	}
);

try
{
	$stub->run();
}
catch (\exception $exception)
{
	$stub->writeError($exception->getMessage());

	exit($exception->getCode());
}

exit(0);


__HALT_COMPILER();
