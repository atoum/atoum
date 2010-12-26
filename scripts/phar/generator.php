<?php

namespace mageekguy\atoum\phar;

use \mageekguy\atoum;

require(__DIR__ . '/../../classes/autoloader.php');

$generator = new atoum\phar\generator(__FILE__);
$generator->setOriginDirectory(__DIR__ . '/../..');
$generator->setStubFile(__DIR__ . '/resources/stub.php');

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
	exit(1);
}

exit(0);

?>
