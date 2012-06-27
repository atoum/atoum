<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum
;

class cli
{
	private static $isTerminal = null;

	public function __construct(atoum\adapter $adapter = null)
	{
		if ($adapter === null)
		{
			$adapter = new atoum\adapter();
		}

		if (self::$isTerminal === null)
		{
			self::$isTerminal = ($adapter->defined('STDOUT') === true && $adapter->function_exists('posix_isatty') === true && $adapter->posix_isatty($adapter->constant('STDOUT')) === true);
		}
	}

	public function setAdapter(atoum\adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function isTerminal()
	{
		return self::$isTerminal;
	}

	public static function forceTerminal()
	{
		self::$isTerminal = true;
	}
}
