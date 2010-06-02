<?php

namespace mageekguy\tests\unit;

use \mageekguy\tests\unit;

abstract class reporter implements \mageekguy\tests\unit\observer
{
	protected $locale = null;

	public function __construct(unit\locale $locale = null)
	{
		if ($locale === null)
		{
			$locale = new unit\locale();
		}

		$this->setLocale($locale);
	}

	public function setLocale(unit\locale $locale)
	{
		$this->locale = $locale;
		return $this;
	}

	protected function getErrorLabel($error)
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
				return $error;
		}
	}
}

?>
