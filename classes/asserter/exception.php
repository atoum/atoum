<?php

namespace atoum\atoum\asserter;

use atoum\atoum;

class exception extends \runtimeException
{
    public function __construct(atoum\asserter $asserter, $message)
    {
        $code = 0;

        $test = $asserter->getTest();

        if ($test !== null) {
            $class = $test->getClass();
            $method = $test->getCurrentMethod();
            $file = $test->getPath();
            $line = null;
            $function = null;

            $backtrace = $this->getBacktrace($test);
            if ($backtrace !== null) {
                $file = $backtrace['file'] ?? $file;

                if (isset($backtrace['line']) === true) {
                    $line = $backtrace['line'];
                }

                if (isset($backtrace['object']) === true && isset($backtrace['function']) === true && $backtrace['object'] === $asserter && $backtrace['function'] !== '__call') {
                    $function = $backtrace['function'];
                }
            }

            $asserterName = preg_replace(
                [
                    '/^' . preg_quote('atoum\atoum\asserters\\') . '/',
                    '/^php(?=.)/'
                ],
                '',
                strtolower(get_class($asserter))
            );

            $code = $test->getScore()->addFail($file, $class, $method, $line, $asserterName . ($function ? '::' . $function : '') . '()', $message);
        }

        parent::__construct($message, $code);
    }


    protected function getBacktrace(atoum\test $test)
    {
        $debugBacktrace = debug_backtrace(false);
        foreach ($debugBacktrace as $key => $value) {
            if (isset($value['class']) === false) {
                continue;
            }

            if (
                $value['class'] === $test->getClass()
                || is_subclass_of($test->getClass(), $value['class'], true)
            ) {
                if (isset($debugBacktrace[$key - 1]) === true) {
                    $key -= 1;
                }

                return $debugBacktrace[$key];
            }
        }

        return null;
    }
}
