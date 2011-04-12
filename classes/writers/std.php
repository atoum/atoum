<?php

namespace mageekguy\atoum\writers;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\report\writers
;

abstract class std extends atoum\writer implements writers\realtime, writers\asynchronous
{
	protected $resource = null;

	public function __destruct()
	{
		if ($this->resource !== null)
		{
			$this->adapter->fclose($this->resource);
		}
	}

	public function write($something)
	{
		return $this->flush($something);
	}

	public function flush($something)
	{
		$this->getResource()->adapter->fwrite($this->resource, $something);

		return $this;
	}

	protected abstract function getResource();
}

?>
