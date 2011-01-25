<?php

namespace mageekguy\atoum\reports;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions;

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

		$this->addRunnerField(new atoum\report\fields\runner\xunit(), array(atoum\runner::runStop));
	}

	public function __toString()
	{
		$string = '';

		foreach ($this->getRunnerFields(atoum\runner::runStop) as $field)
		{
			$string .= $field;
		}

		return $string;
	}
	
	public function runnerStop(atoum\runner $runner)
	{
		parent::runnerStop($runner);

		return $this->write();
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
}

?>
