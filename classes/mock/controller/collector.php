<?php

namespace mageekguy\atoum\mock\controller;

use
	mageekguy\atoum\mock
;

class collector
{
	protected static $instances = null;

	public static function add(mock\aggregator $mock, mock\controller $controller)
	{
		self::$instances[$mock] = $controller;

		return $controller->control($mock);
	}

	public static function get(mock\aggregator $mock)
	{
		return (isset(self::$instances[$mock]) === false ? null : self::$instances[$mock]);
	}

	public static function remove(mock\aggregator $mock)
	{
		if (isset(self::$instances[$mock]) === true)
		{
			self::$instances[$mock]->reset();

			unset(self::$instances[$mock]);
		}
	}

	public static function clean()
	{
		self::$instances = new \splObjectStorage();
	}
}

collector::clean();
