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
		parent::handleEvent($event, $observable)->write($event);

		if ($event === atoum\runner::runStop)
		{
			foreach ($this->writers as $writer)
			{
				$writer->reset();
			}
		}

		return $this;
	}

	public function addWriter(report\writers\realtime $writer)
	{
		return $this->doAddWriter($writer);
	}

	public function isOverridableBy(report $report)
	{
		return ($report instanceof self) === false;
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
