<?php

namespace mageekguy\atoum\writers;

use mageekguy\atoum;

class stderr extends atoum\writer
{
	public function write($string)
	{
		return $this->flush($string);
	}

	public function flush($string)
	{
		$this->adapter->fwrite(STDERR, rtrim($string) . "\n");
		return $this;
	}
}

?>
