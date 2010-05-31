<?php

namespace mageekguy\tests\unit\reporters;

class cli extends \mageekguy\tests\unit\reporter
{
	public function report(\mageekguy\tests\unit\test $test)
	{
		self::write('\mageekguy\tests\unit\test version ' . $test->getVersion() . ' by ' . \mageekguy\tests\unit\test::author) . '.';

		$score = $test->getScore();

		$failNumber = $score->getFailNumber();

		if ($failNumber > 0)
		{
			self::write();

			if ($failNumber > 1)
			{
				self::write('There were ' . $failNumber . ' failures:');
			}
			else
			{
				self::write('There was ' . $failNumber . ' failure:');
			}

			self::write();

			foreach ($score->getAssertions() as $class => $methods)
			{
				foreach ($methods as $method => $assertions)
				{
					$fails = array();

					foreach ($assertions as $assertion)
					{
						if ($assertion['fail'] !== null)
						{
							$fails[] = $assertion;
						}
					}

					if (sizeof($fails) > 0)
					{
						self::write($class . '::' . $method . '():', 1);

						foreach ($fails as $fail)
						{
							self::write($fail['asserter'] . ' failed because ' . $fail['fail'] . ' in file ' . $fail['file'] . ' at line ' . $fail['line'], 2);
						}

						self::write();
					}
				}
			}
		}

		$errorNumber = $score->getErrorNumber();

		if ($errorNumber > 0)
		{
			if ($errorNumber > 1)
			{
				self::write('There were ' . $errorNumber . ' errors:');
			}
			else
			{
				self::write('There was ' . $errorNumber . ' error:');
			}

			foreach ($score->getErrors() as $class => $methods)
			{
				foreach ($methods as $method => $errors)
				{
					self::write($class . '::' . $method . '():', 1);

					foreach ($errors as $error)
					{
						self::write('[' . $error['type'] . '] ' . $error['message'], 2);
					}
				}
			}
		}
	}

	public static function write($message = '', $level = 0)
	{
		if ($message != '')
		{
			echo ($level <= 0 ? '' : str_repeat("\t", $level)) . ltrim($message);
		}

		echo PHP_EOL;
	}
}

?>
