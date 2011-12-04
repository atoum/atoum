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

		$errorHandler = set_error_handler(function($error, $message, $file, $line, $context) use (& $errors) {
				$errors[] = func_get_args();
			}
		);

		$closure = $closure ?: function($path) { include_once($path); };

		$closure($path);

		restore_error_handler();

		if (in_array($path, get_included_files()) === false)
		{
			throw new includer\exception('Unable to include \'' . $path . '\'');
		}
		else if ($errorHandler !== null)
		{
			foreach ($errors as $error)
			{
				call_user_func_array($errorHandler, $error);
			}
		}

		return $this;
	}
}

?>
