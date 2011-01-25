<?php

namespace mageekguy\atoum\reports;

use \mageekguy\atoum;

class xunit extends atoum\report
{
	protected $adapter;
	
	public function __construct(atoum\adapter $adapter = null)
	{
		parent::__construct();
    	if ($adapter === null)
		{
			$adapter = new atoum\adapter();
		}
		$this->adapter = $adapter;
		$xmlLoaded = $this->adapter->extension_loaded('libxml');

		if ($xmlLoaded !== true)
		{
			throw new atoum\exceptions\runtime('libxml is mandatory for xunit report.');
		}
		$this->addRunnerField(new atoum\report\fields\runner\xunit(), array(atoum\runner::runStop));
	}

	public function __toString()
	{
		$string = '';
		$fields = $this->getRunnerFields(atoum\runner::runStop);
		foreach ($fields as $field)
		{
			$string .= (string)$field;
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
