<?php

namespace mageekguy\atoum\scripts\svn\builder;

use \mageekguy\atoum;
use \mageekguy\atoum\scripts;

require_once(__DIR__ . '/../../classes/autoloader.php');

$generator = new scripts\svn\builder(__FILE__);

set_error_handler(function($error, $message, $file, $line) use ($generator) {
		$generator->writeError(sprintf($generator->getLocale()->_('Unattended error: %s'), $message));
		exit($error);
	}
);

try
{
	$generator->run();
}
catch (\runtimeException $exception)
{
	$generator->writeError($exception->getMessage());
	exit(1);
}
catch (\exception $exception)
{
	$generator->writeError(sprintf($generator->getLocale()->_('Unattended exception: %s'), $exception->getMessage()));
	exit(2);
}

exit(0);

?>
