<?php

namespace mageekguy\tests\unit;

use \mageekguy\tests\unit\reporters;

require(__DIR__ . '/autoloader.php');

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);
}

class autorunner
{
	const testClass = '\mageekguy\tests\unit\test';

	public static function run()
	{
		foreach (get_declared_classes() as $class)
		{
			if (self::isTestClass($class) === true)
			{
				self::runTestClass($class);
			}
		}
	}

	protected static function isTestClass($class)
	{
		return (is_subclass_of($class, self::testClass) === true && get_parent_class($class) !== false);
	}

	protected static function runTestClass($testClass)
	{
		$reporter = new reporters\cli();
		$test = new $testClass();
		$test->addObserver($reporter);
		$test->run();
	}
}

if (autorun === true)
{
	register_shutdown_function(function()
		{
			\mageekguy\tests\unit\autorunner::run();
		}
	);
}

?>
