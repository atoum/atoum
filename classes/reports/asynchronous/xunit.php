<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\report\fields
;

class xunit extends atoum\reports\asynchronous
{
	protected $adapter = null;

	public function __construct(atoum\adapter $adapter = null)
	{
		parent::__construct(null, $adapter);

		if ($this->adapter->extension_loaded('libxml') === false)
		{
			throw new exceptions\runtime('libxml PHP extension is mandatory for xunit report');
		}

		$this->addRunnerField(new fields\runner\xunit(), array(atoum\runner::runStop));
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setAdapter(atoum\adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function runnerStop(atoum\runner $runner)
	{
		return parent::runnerStop($runner)->write();
	}
}

?>
