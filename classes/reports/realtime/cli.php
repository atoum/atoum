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

		$resultField = new fields\runner\result\cli();
		$resultField->setSuccessColorizer(new atoum\cli\colorizer('0;37', '42'));
		$resultField->setFailureColorizer(new atoum\cli\colorizer('0;37', '41'));

		$this
			->addRunnerField(new fields\runner\version\cli(), array(atoum\runner::runStart))
			->addRunnerField(new fields\runner\php\cli(), array(atoum\runner::runStart))
			->addRunnerField(new fields\runner\tests\duration\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\tests\memory\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\tests\coverage\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\duration\cli(), array(atoum\runner::runStop))
			->addRunnerField($resultField, array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\failures\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\outputs\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\errors\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\exceptions\cli(), array(atoum\runner::runStop))
			->addTestField(new fields\test\run\cli(), array(atoum\test::runStart))
			->addTestField(new fields\test\event\cli())
			->addTestField(new fields\test\duration\cli(), array(atoum\test::runStop))
			->addTestField(new fields\test\memory\cli(), array(atoum\test::runStop))
		;
	}
}

?>
