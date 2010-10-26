<?php

namespace mageekguy\atoum\phar;

use \mageekguy\atoum;

require(__DIR__ . '/../../classes/autoloader.php');

$generator = new atoum\phar\generator(__FILE__);
$generator->setOriginDirectory(__DIR__ . '/../..');

set_error_handler(function($error, $message, $file, $line) use ($generator) {
		$generator->getErrorWriter()->write(sprintf($generator->getLocale()->_('Unattended error: %s'), $message));
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
	exit($exception->getCode());
}
catch (\exception $exception)
{
	$generator->getErrorWriter()->write(sprintf($generator->getLocale()->_('Unattended exception: %s'), $exception->getMessage()));
	exit($exception->getCode());
}

exit(0);

?>
