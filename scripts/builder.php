<?php

use atoum\atoum\scripts;

require_once __DIR__ . '/../classes/autoloader.php';

$builder = new scripts\builder(__FILE__);

set_error_handler(
    function ($error, $message, $file, $line) use ($builder) {
        if (error_reporting() & $error) {
            $builder->writeError($message);

            exit($error);
        }
    }
);

try {
    $builder->run();
} catch (\exception $exception) {
    $builder->writeError($exception->getMessage());

    exit($exception->getCode());
}

exit(0);
