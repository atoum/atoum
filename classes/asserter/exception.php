<?php

namespace mageekguy\atoum\asserter;

use
	mageekguy\atoum
;

class exception extends \runtimeException
{
	public function __construct(atoum\asserter $asserter, $message)
	{
		$code = 0;

		$test = $asserter->getTest();

		if ($test !== null)
		{
			$class = $test->getClass();
			$method = $test->getCurrentMethod();
			$file = $test->getPath();
			$line = null;
			$function = null;

			foreach (array_filter(debug_backtrace(false), function($backtrace) use ($file) { return isset($backtrace['file']) === true && $backtrace['file'] === $file; }) as $backtrace)
			{
				if ($line === null && isset($backtrace['line']) === true)
				{
					$line = $backtrace['line'];
				}

				if ($function === null && isset($backtrace['object']) === true && isset($backtrace['function']) === true && $backtrace['object'] === $asserter && $backtrace['function'] !== '__call')
				{
					$function = $backtrace['function'];
				}
			}

			$code = $test->getScore()->addFail($file, $class, $method, $line, get_class($asserter) . ($function ? '::' . $function : '') . '()', $message);
		}

		parent::__construct($message, $code);
	}
}
