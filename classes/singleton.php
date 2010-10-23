<?php

namespace mageekguy\atoum;

use mageekguy\atoum;

abstract class singleton
{
	public static function getInstance()
	{
		static $instances = array();

		$class = get_called_class();

		if (isset($instances[$class]) === false)
		{
			$instances[$class] = new $class();
		}

		return $instances[$class];
	}

	protected function __construct() {}

	protected function __clone() {}
}

?>
