<?php

namespace mageekguy\atoum\reports\realtime;

use
	\mageekguy\atoum,
	\mageekguy\atoum\reports,
	\mageekguy\atoum\report\fields
;

class cli extends reports\realtime
{
	public function __construct()
	{
		parent::__construct();

		$this
			->addRunnerField(new fields\runner\version\string(), array(atoum\runner::runStart))
			->addRunnerField(new fields\runner\php\string(), array(atoum\runner::runStart))
			->addRunnerField(new fields\runner\tests\duration\string(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\tests\memory\string(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\tests\coverage\string(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\duration\string(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\result\string(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\failures\string(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\outputs\string(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\errors\string(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\exceptions\string(), array(atoum\runner::runStop))
			->addTestField(new fields\test\run\string(), array(atoum\test::runStart))
			->addTestField(new fields\test\event\string())
			->addTestField(new fields\test\duration\string(), array(atoum\test::runStop))
			->addTestField(new fields\test\memory\string(), array(atoum\test::runStop))
		;
	}
}

?>
