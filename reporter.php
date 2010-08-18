<?php

namespace mageekguy\atoum;

abstract class reporter implements observer
{
	protected $locale = null;

	public function __construct(locale $locale = null)
	{
		if ($locale === null)
		{
			$locale = new locale();
		}

		$this->setLocale($locale);
	}

	public function setLocale(locale $locale)
	{
		$this->locale = $locale;
		return $this;
	}

	public function manageObservableEvent(observable $observable, $event)
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
						$this->beforeTestMethod($observable);
						break;

					case test::eventSuccess:
						$this->success($observable);
						break;

					case test::eventFailure:
						$this->failure($observable);
						break;

					case test::eventError:
						$this->error($observable);
						break;

					case test::eventException:
						$this->exception($observable);
						break;

					case test::eventAfterTestMethod:
						$this->afterTestMethod($observable);
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

	protected function runStart(runner $runner)
	{
		return $this;
	}

	protected function testRunStart(test $test)
	{
		return $this;
	}

	protected function beforeTestMethod(test $test)
	{
		return $this;
	}

	protected function success(test $test)
	{
		return $this;
	}

	protected function failure(test $test)
	{
		return $this;
	}

	protected function error(test $test)
	{
		return $this;
	}

	protected function exception(test $test)
	{
		return $this;
	}

	protected function afterTestMethod(test $test)
	{
		return $this;
	}

	protected function testRunEnd(test $test)
	{
		return $this;
	}

	protected function runEnd(runner $runner)
	{
		return $this;
	}
}

?>
