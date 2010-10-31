<?php

namespace mageekguy\atoum\report;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

abstract class decorator
{
	protected $writers = array();

	public function addWriter(atoum\writer $writer)
	{
		$this->writers[] = $writer;

		return $this;
	}

	public function getWriters()
	{
		return $this->writers;
	}

	public abstract function write(report\field $field);
	public abstract function flush(report\field $field);

	protected function sendToWriters($something)
	{
		foreach ($this->writers as $writer)
		{
			$writer->write($something);
		}

		return $this;
	}
}

?>
