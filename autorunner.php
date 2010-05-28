<?php

namespace mageekguy\tests\unit;

require(__DIR__ . '/autoloader.php');

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);
}

class autorunner
{
	const testClass = '\mageekguy\tests\unit\test';

	public function run()
	{
		foreach (get_declared_classes() as $class)
		{
			if (self::isTestClass($class) === true)
			{
				self::runTestClass($class);
			}
		}

		return $this;
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

			$stdout = stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			$stderr = stream_get_contents($pipes[2]);
			fclose($pipes[2]);

			$returnValue = proc_close($php);

			var_dump(unserialize($stdout));
			var_dump($stderr);
			var_dump($returnValue);
		}
	}
}

if (autorun === true)
{
	register_shutdown_function(function()
		{
			$autorunner = new \mageekguy\tests\unit\autorunner(); $autorunner->run();
		}
	);
}

?>
