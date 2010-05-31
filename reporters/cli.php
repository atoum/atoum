<?php

namespace mageekguy\tests\unit\reporters;

class cli extends \mageekguy\tests\unit\reporter
{
	public function report(\mageekguy\tests\unit\test $test)
	{
		self::write('\mageekguy\tests\unit\test version ' . $test->getVersion() . ' by ' . \mageekguy\tests\unit\test::author);

		$score = $test->getScore();

		foreach ($score->getAssertions() as $class => $methods)
		{
			foreach ($methods as $method => $assertions)
			{
				self::write($class . '::' . $method . '():');

				foreach ($assertions as $assertion)
				{
					self::write($assertion['asserter'] . ' ' . ($assertion['fail'] === null ? 'pass' : 'failed because ' . $assertion['fail']) . ' in file ' . $assertion['file'] . ' at line ' . $assertion['line']);
				}
			}
		}

		foreach ($score->getErrors() as $class => $methods)
		{
			foreach ($methods as $method => $errors)
			{
				self::write('Error in ' . $class . '::' . $method . '():');

				foreach ($errors as $error)
				{
					self::write('[' . $error['type'] . '] ' . $error['message']);
				}
			}
		}
	}

	public static function write($message)
	{
		echo ltrim($message) . "\n";
	}
}

?>
