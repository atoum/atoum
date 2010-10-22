<?php

namespace mageekguy\atoum;

use mageekguy\atoum;

abstract class singleton
{
	public static function getInstance()
	{
		static $instance = null;

		if ($instance === null)
		{
			$instance = new static();
		}

		return $instance;
	}

	protected function __construct() {}

	protected function __clone() {}
}

?>
