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
		$test = new $testClass();

		foreach ($test->getTestMethods() as $testMethod)
		{
			self::runTestMethod($test, $testMethod);
		}

		$reporter = new reporters\cli();

		$reporter->report($test);
	}

	protected static function runTestMethod($test, $testMethod)
	{
		$phpCode = '<?php define(\'' . __NAMESPACE__ . '\autorun\', false); require(\'' . $test->getPath() . '\'); $unit = new ' . $test->getClass() . '; $unit->run(array(\'' . $testMethod . '\')); echo serialize($unit->getScore()); ?>';

		$descriptors = array
			(
				0 => array('pipe', 'r'),
				1 => array('pipe', 'w'),
				2 => array('pipe', 'w')
			);

		$php = proc_open($_SERVER['_'], $descriptors, $pipes);

		if ($php !== false)
		{
			fwrite($pipes[0], $phpCode);
			fclose($pipes[0]);

			$stdOut = stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			$stdErr = stream_get_contents($pipes[2]);
			fclose($pipes[2]);

			$returnValue = proc_close($php);

			if ($stdErr != '')
			{
				throw new \runtimeException($stdErr, $returnValue);
			}

			$score = unserialize($stdOut);

			if ($score instanceof \mageekguy\tests\unit\score === false)
			{
				throw new \runtimeException('Unable to retrieve score from \'' . $stdOut . '\'');
			}

			$test->getScore()->merge($score);
		}
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
