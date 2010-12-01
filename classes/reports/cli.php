<?php

namespace mageekguy\atoum\reports;

use \mageekguy\atoum;

class cli extends atoum\report
{
	public function __construct()
	{
		parent::__construct();

		$this->addRunnerField(new atoum\report\fields\runner\version(), array(atoum\runner::runStart));
		$this->addRunnerField(new atoum\report\fields\runner\tests\duration(), array(atoum\runner::runStop));
		$this->addRunnerField(new atoum\report\fields\runner\tests\memory(), array(atoum\runner::runStop));
		$this->addRunnerField(new atoum\report\fields\runner\tests\coverage(), array(atoum\runner::runStop));
		$this->addRunnerField(new atoum\report\fields\runner\duration(), array(atoum\runner::runStop));
		$this->addRunnerField(new atoum\report\fields\runner\result(), array(atoum\runner::runStop));
		$this->addRunnerField(new atoum\report\fields\runner\failures(), array(atoum\runner::runStop));
		$this->addRunnerField(new atoum\report\fields\runner\outputs(), array(atoum\runner::runStop));
		$this->addRunnerField(new atoum\report\fields\runner\errors(), array(atoum\runner::runStop));
		$this->addRunnerField(new atoum\report\fields\runner\exceptions(), array(atoum\runner::runStop));

		$this->addTestField(new atoum\report\fields\test\run(), array(atoum\test::runStart));
		$this->addTestField(new atoum\report\fields\test\event());
		$this->addTestField(new atoum\report\fields\test\duration(), array(atoum\test::runStop));
		$this->addTestField(new atoum\report\fields\test\memory(), array(atoum\test::runStop));

		$stringDecorator = new atoum\report\decorators\string();
		$stringDecorator->addWriter(new atoum\writers\stdout());

		$this->addDecorator($stringDecorator);
	}
}

?>
