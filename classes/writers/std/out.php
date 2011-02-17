<?php

namespace mageekguy\atoum\writers\std;

use mageekguy\atoum;
use mageekguy\atoum\writers;

class out extends writers\std
{
	protected $resource = null;

	public function __construct(atoum\adapter $adapter = null)
	{
		parent::__construct($adapter, 'php://stdout');
	}
}

?>
