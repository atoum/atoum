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
			if ($adapter->defined('STDOUT') === false)
			{
				self::$isTerminal = false;
			}
			else
			{
				$stat = $adapter->fstat($adapter->constant('STDOUT'));
				// please, see <sys/stat.h>.
				$mode = $stat['mode'] & 0170000;
				self::$isTerminal = $mode === 0020000;

				if ($adapter->defined('PHP_WINDOWS_VERSION_BUILD') === true)
				{
					self::$isTerminal = self::$isTerminal && (Boolean) $adapter->getenv('ANSICON');
				}
			}
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
