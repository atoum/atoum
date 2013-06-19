<?php

require_once __DIR__ . '/runner.php';

if (class_exists('\\PHPUnit_Framework_TestCase') === false)
{
	class PHPUnit_Framework_TestCase extends \mageekguy\atoum\test\phpunit\test {}
}
