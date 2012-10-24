<?php

namespace mageekguy\atoum\report\fields\runner;

use
	mageekguy\atoum\locale,
	mageekguy\atoum\runner,
	mageekguy\atoum\report,
	mageekguy\atoum\observable
;

abstract class errors extends report\field
{
	protected $runner = null;

	public function __construct()
	{
		parent::__construct(array(runner::runStop));
	}

	public function getRunner()
	{
		return $this->runner;
	}

	public function handleEvent($event, observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}
		else
		{
			$this->runner = $observable;

			return true;
		}
	}

	public static function getType($error)
	{
		switch ($error)
		{
			case E_ERROR:
				return 'E_ERROR';

			case E_WARNING:
				return 'E_WARNING';

			case E_NOTICE:
				return 'E_NOTICE';

			case E_USER_NOTICE:
				return 'E_USER_NOTICE';

			case E_USER_WARNING:
				return 'E_USER_WARNING';

			case E_USER_ERROR:
				return 'E_USER_ERROR';

			case E_RECOVERABLE_ERROR:
				return 'E_RECOVERABLE_ERROR';

			case E_DEPRECATED:
				return 'E_DEPRECATED';

			case E_USER_DEPRECATED:
				return 'E_USER_DEPRECATED';

			default:
				return strtoupper($error);
		}
	}
}
