<?php

namespace mageekguy\atoum\reports;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions;
use \mageekguy\atoum\report\fields;

class xunit extends atoum\report
{
	protected $adapter = null;

	public function __construct(atoum\adapter $adapter = null)
	{
    	if ($adapter === null)
		{
			$adapter = new atoum\adapter();
		}

		$this->setAdapter($adapter);

		if ($this->adapter->extension_loaded('libxml') === false)
		{
			throw new exceptions\runtime('libxml PHP extension is mandatory for xunit report');
		}

		parent::__construct();

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
