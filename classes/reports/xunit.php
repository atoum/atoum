<?php

namespace mageekguy\atoum\reports;

use \mageekguy\atoum;

class xunit extends atoum\report
{
	public function __construct()
	{
		parent::__construct();
    
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
}

?>
