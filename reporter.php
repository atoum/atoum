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

	public function manageObservableEvent(\mageekguy\tests\unit\observable $observable, $event)
	{
		switch (true)
		{
			case $observable instanceof runner:
				switch ($event)
				{
					case runner::eventRunStart:
						$this->runStart($observable);
						break;

					case runner::eventRunStop:
						$this->runEnd($observable);
						break;
				}
				break;

			case $observable instanceof test:
				switch ($event)
				{
					case test::eventRunStart:
						$this->testRunStart($observable);
						break;

					case test::eventBeforeTestMethod:
						$this->testMethods++;
						break;

					case test::eventSuccess:
						$this->progressBar('.');
						break;

					case test::eventFailure:
						$this->progressBar('F');
						break;

					case test::eventError:
					case test::eventException:
						$this->progressBar('!');
						break;

					case test::eventRunEnd:
						$this->testRunEnd($observable);
						break;
				}
				break;
		}
	}

	public static function getErrorLabel($error)
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

	protected function runStart(\mageekguy\tests\unit\runner $runner)
	{
		return $this;
	}

	protected function testRunStart(\mageekguy\tests\unit\test $test)
	{
		return $this;
	}

	protected function beforeTestMethod(\mageekguy\tests\unit\test $test)
	{
		return $this;
	}

	protected function success(\mageekguy\tests\unit\test $test)
	{
		return $this;
	}

	protected function failure(\mageekguy\tests\unit\test $test)
	{
		return $this;
	}

	protected function error(\mageekguy\tests\unit\test $test)
	{
		return $this;
	}

	protected function exception(\mageekguy\tests\unit\test $test)
	{
		return $this;
	}

	protected function afterTestMethod(\mageekguy\tests\unit\test $test)
	{
		return $this;
	}

	protected function testRunEnd(\mageekguy\tests\unit\test $test)
	{
		return $this;
	}

	protected function runEnd(\mageekguy\tests\unit\runner $runner)
	{
		return $this;
	}
}

?>
