<?php

namespace mageekguy\atoum;

abstract class reporter implements observers\runner, observers\test
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

	public function runnerStart(runner $runner)
	{
		return $this;
	}

	public function testRunStart(test $test)
	{
		return $this;
	}

	public function beforeTestSetup(test $test)
	{
		return $this;
	}

	public function afterTestSetup(test $test)
	{
		return $this;
	}

	public function beforeTestMethod(test $test)
	{
		return $this;
	}

	public function testAssertionSuccess(test $test)
	{
		return $this;
	}

	public function testAssertionFail(test $test)
	{
		return $this;
	}

	public function testError(test $test)
	{
		return $this;
	}

	public function testException(test $test)
	{
		return $this;
	}

	public function afterTestMethod(test $test)
	{
		return $this;
	}

	public function testRunStop(test $test)
	{
		return $this;
	}

	public function beforeTestTearDown(test $test)
	{
		return $this;
	}

	public function afterTestTearDown(test $test)
	{
		return $this;
	}

	public function runnerStop(runner $runner)
	{
		return $this;
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
}

?>
