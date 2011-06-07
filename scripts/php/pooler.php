<?php

namespace mageekguy\atoum\php;

use
	\mageekguy\atoum,
	\mageekguy\atoum\scripts
;

require_once(__DIR__ . '/../../classes/autoloader.php');

$pooler = new scripts\php\pooler(__FILE__);
$pooler->setWorkingDirectory(__DIR__ . '/../../tmp');

set_error_handler(function($error, $message, $file, $line) use ($pooler) {
		if (error_reporting() !== 0)
		{
			$pooler->writeError($message);

			exit($error);
		}
	}
);

try
{
	$pooler->run();
}
catch (\exception $exception)
{
	$pooler->writeError($exception->getMessage());

	exit($exception->getCode());
}

exit(0);

?>
