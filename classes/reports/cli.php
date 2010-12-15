<?php

namespace mageekguy\atoum\reports;

use \mageekguy\atoum;

class cli extends atoum\report
{
	public function __construct()
	{
		parent::__construct();

		$this
			->addRunnerField(new atoum\report\fields\runner\version\string(), array(atoum\runner::runStart))
			->addRunnerField(new atoum\report\fields\runner\tests\duration\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\tests\memory\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\tests\coverage\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\duration\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\result\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\failures\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\outputs\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\errors\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\exceptions\string(), array(atoum\runner::runStop))
			->addTestField(new atoum\report\fields\test\run\string(), array(atoum\test::runStart))
			->addTestField(new atoum\report\fields\test\event\string())
			->addTestField(new atoum\report\fields\test\duration\string(), array(atoum\test::runStop))
			->addTestField(new atoum\report\fields\test\memory\string(), array(atoum\test::runStop))
		;

		$stringDecorator = new atoum\report\decorators\string();
		$stringDecorator->addWriter(new atoum\writers\stdout());

		$this->addDecorator($stringDecorator);
	}
}

?>
