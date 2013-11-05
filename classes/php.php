<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\cli
;

class php extends cli\command
{
	public function setBinaryPath($phpPath = null)
	{
		if ($phpPath === null)
		{
			if ($this->adapter->defined('PHP_BINARY') === true)
			{
				$phpPath = $this->adapter->constant('PHP_BINARY');
			}

			if ($phpPath === null)
			{
				$phpPath = $this->adapter->getenv('PHP_PEAR_PHP_BIN');

				if ($phpPath === false)
				{
					$phpPath = $this->adapter->getenv('PHPBIN');

					if ($phpPath === false)
					{
						$phpDirectory = $this->adapter->constant('PHP_BINDIR');

						if ($phpDirectory === null)
						{
							throw new exceptions\runtime('Unable to find PHP executable');
						}

						$phpPath = $phpDirectory . '/php';
					}
				}
			}
		}

		return parent::setBinaryPath($phpPath);
	}
}
