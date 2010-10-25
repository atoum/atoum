<?php

namespace mageekguy\atoum\writers;

use mageekguy\atoum;

class stderr extends atoum\writer
{
	public function write($string)
	{
		$this->adapter->fwrite(STDERR, rtrim($string) . "\n");
		return $this;
	}
}

?>
