<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\includer
;

class includer
{
	public function includePath($path, \closure $closure = null)
	{
		$errors = array();

		$path = (string) $path;

		$errorHandler = set_error_handler(function($error, $message, $file, $line, $context) use (& $errors) {
				$errorReporting = error_reporting();

				if ($errorReporting !== 0 && $errorReporting & $error)
				{
					$errors[] = func_get_args();
				}
			}
		);

		$closure = $closure ?: function($path) { include_once($path); };

		$closure($path);

		restore_error_handler();

		if (sizeof($errors) > 0)
		{
			$realpath = parse_url($path, PHP_URL_SCHEME) !== null ? $path : realpath($path) ?: $path;

			if (in_array($realpath, get_included_files(), true) === false)
			{
				throw new includer\exception('Unable to include \'' . $path . '\'');
			}

			if ($errorHandler !== null)
			{
				foreach ($errors as $error)
				{
					call_user_func_array($errorHandler, $error);
				}
			}
		}

		return $this;
	}
}
