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
			self::$isTerminal = $adapter->defined('STDOUT');

			if (self::$isTerminal === true)
			{
				$stdoutStat = $adapter->fstat($adapter->constant('STDOUT'));

				self::$isTerminal = (($stdoutStat['mode'] & 0170000) === 0020000); // See <sys/stat.h> for more information.

				if (self::$isTerminal === true && $adapter->defined('PHP_WINDOWS_VERSION_BUILD') === true)
				{
					self::$isTerminal = (self::$isTerminal && $adapter->getenv('ANSICON') == true);
				}
			}
		}
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
