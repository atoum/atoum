<?php

namespace mageekguy\atoum\phar;

use \mageekguy\atoum\phar;

require_once('phar://' . __FILE__ . '/classes/autoloader.php');

if (PHP_SAPI === 'cli')
{
	$stub = new phar\stub(__FILE__);

	set_error_handler(function($errno, $errstring) use ($stub) {
			echo sprintf($stub->getLocale()->_('Error: %s'), $errstring) . PHP_EOL;
		}
	);

	set_exception_handler(function(\exception $exception) use ($stub) {
			echo sprintf($stub->getLocale()->_('Error: %s'), $exception->getMessage()) . PHP_EOL;
		}
	);

	$stub->run();
}

__HALT_COMPILER();
