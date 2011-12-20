<?php

namespace mageekguy\atoum\reports;

use
	mageekguy\atoum,
	mageekguy\atoum\report
;

abstract class asynchronous extends atoum\report
{
	protected $string = '';
	protected $fail = false;

	public function __toString()
	{
		return $this->string;
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		parent::handleEvent($event, $observable)->getFieldsAsString($event);

		switch ($event)
		{
			case atoum\test::fail:
			case atoum\test::error:
			case atoum\test::exception:
				$this->fail = true;
				break;

			case atoum\runner::runStop:
				if ($this->title !== null)
				{
					$this->title = sprintf($this->title, $this->adapter->date($this->locale->_('Y-m-d')), $this->adapter->date($this->locale->_('H:i:s')), $this->fail === true ? $this->locale->_('FAIL') : $this->locale->_('SUCCESS'));
				}

				foreach ($this->writers as $writer)
				{
					$writer->writeAsynchronousReport($this);
				}
				break;
		}

		return $this;
	}

	public function addWriter(report\writers\asynchronous $writer)
	{
		return $this->doAddWriter($writer);
	}

	protected function getFieldsAsString($event)
	{
		foreach ($this->lastSetFields as $field)
		{
			$this->string .= (string) $field;
		}

		return $this;
	}
}

?>
