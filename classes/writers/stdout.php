<?php

namespace mageekguy\atoum\writers;

use mageekguy\atoum;

class stdout extends atoum\writer
{
	public function write($string)
	{
		$this->adapter->fwrite(STDOUT, rtrim($string) . "\n");
		return $this;
	}
}

?>
