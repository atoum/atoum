<?php

require_once __DIR__ . '/runner.php';

if (class_exists('\\PHPUnit_Framework_TestCase') === false)
{
	class PHPUnit_Framework_TestCase extends \mageekguy\atoum\test\phpunit\test {}
}

$aliases = array(
	'PHPUnit_Framework_AssertionFailedError' => 'mageekguy\atoum\asserter\exception'
);

foreach ($aliases as $phpunitClass => $atoumClass)
{
	if (class_exists($phpunitClass))
	{
		throw new \mageekguy\atoum\exceptions\logic(sprintf('Class %s already exists', $phpunitClass));
	}

	class_alias($atoumClass, $phpunitClass);
}
