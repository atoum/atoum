<?php

namespace mageekguy\atoum\scripts\svn\builder;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts
;

require_once __DIR__ . '/../classes/autoloader.php';

$builder = new scripts\builder(__FILE__);

set_error_handler(function($error, $message, $file, $line) use ($builder) {
		if (error_reporting() !== 0)
		{
			$builder->writeError(sprintf($builder->getLocale()->_('Error: %s'), $message));
			exit($error);
		}
	}
);

try
{
	$builder->run();
}
catch (\exception $exception)
{
	$builder->writeError(sprintf($builder->getLocale()->_('Exception: %s'), $exception->getMessage()));
	exit($exception->getCode());
}

exit(0);
