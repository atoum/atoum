<?php

namespace mageekguy\atoum;

class cli
{
	protected $adapter = null;

	private static $isTerminal = null;

	public function __construct(adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new adapter();
	}

	public function isTerminal()
	{
		$isTerminal = self::$isTerminal;

		if ($isTerminal === null)
		{
			$isTerminal = $this->adapter->defined('STDOUT');

			if ($isTerminal === true)
			{
				$stdoutStat = $this->adapter->fstat($this->adapter->constant('STDOUT'));

				$isTerminal = (($stdoutStat['mode'] & 0170000) === 0020000); // See <sys/stat.h> for more information.

				if ($isTerminal === true && $this->adapter->defined('PHP_WINDOWS_VERSION_BUILD') === true)
				{
					$isTerminal = ($isTerminal && $this->adapter->getenv('ANSICON') == true);
				}
			}
		}

		return $isTerminal;
	}

	public static function forceTerminal()
	{
		self::$isTerminal = true;
	}
}
