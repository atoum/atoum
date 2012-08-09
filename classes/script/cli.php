<?php

namespace mageekguy\atoum\script;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

abstract class cli extends atoum\script
{
	public function __construct($name, atoum\factory $factory = null)
	{
		parent::__construct($name, $factory);

		if ($this->adapter->php_sapi_name() !== 'cli')
		{
			throw new exceptions\logic('\'' . $this->getName() . '\' must be used in CLI only');
	 	}
	}

}
