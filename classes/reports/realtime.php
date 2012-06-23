<?php

namespace mageekguy\atoum\reports;

use
	mageekguy\atoum,
	mageekguy\atoum\report
;

abstract class realtime extends atoum\report
{
	public function handleEvent($event, atoum\observable $observable)
	{
		return parent::handleEvent($event, $observable)->write($event);
	}

	public function addWriter(report\writers\realtime $writer)
	{
		return $this->doAddWriter($writer);
	}

	protected function write($event)
	{
		foreach ($this->writers as $writer)
		{
			$writer->writeRealtimeReport($this, $event);
		}

		return $this;
	}
}
