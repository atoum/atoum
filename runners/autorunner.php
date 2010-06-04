<?php

namespace mageekguy\tests\unit\runners;

require_once(__DIR__ . '/../runner.php');

use \mageekguy\tests\unit;
use \mageekguy\tests\unit\reporters;

if (defined(__NAMESPACE__ . '\autorun') === false)
{
	define(__NAMESPACE__ . '\autorun', true);
}

class autorunner extends \mageekguy\tests\unit\runner
{
	public function run()
	{
		$locale = new unit\locale();

		$reporter = new reporters\cli();

		$this->addObserver($reporter);

		$this->sendEventToObservers(self::eventRunStart);

		foreach (get_declared_classes() as $class)
		{
			if (self::isTestClass($class) === true)
			{
				$test = new $class($this->score, $locale);
				$test->addObserver($reporter);
				$test->run();
			}
		}

		$this->sendEventToObservers(self::eventRunStop);
	}

	protected static function isTestClass($class)
	{
		return (is_subclass_of($class, self::testClass) === true && get_parent_class($class) !== false);
	}
}

if (autorun === true)
{
	register_shutdown_function(function()
		{
			$autorunner = new \mageekguy\tests\unit\runners\autorunner();
			$autorunner->run();
		}
	);
}

?>
