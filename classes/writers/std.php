<?php

namespace mageekguy\atoum\writers;

use mageekguy\atoum;
use mageekguy\atoum\exceptions;

abstract class std extends atoum\writer
{
	protected $resource = null;

	public function __construct(atoum\adapter $adapter = null, $stream)
	{
		parent::__construct($adapter);

		$this->resource = $this->adapter->fopen($stream, 'w');

		if ($this->resource === false)
		{
			throw new exceptions\runtime('Unable to open ' . $stream . ' stream');
		}
	}

	public function __destruct()
	{
		if (is_resource($this->resource) === true)
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
		$this->adapter->fwrite($this->resource, $something);
		return $this;
	}
}

?>
