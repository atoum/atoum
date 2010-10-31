<?php

namespace mageekguy\atoum\writers;

use mageekguy\atoum;

class stdout extends atoum\writer
{
	public function write($something)
	{
		return $this->flush($something);
	}

	public function flush($something)
	{
		$this->adapter->fwrite(STDOUT, rtrim($something) . "\n");
		return $this;
	}
}

?>
