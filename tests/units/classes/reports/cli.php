<?php

namespace mageekguy\atoum\tests\units\reports;

use \mageekguy\atoum;
use \mageekguy\atoum\report;
use \mageekguy\atoum\reports;

require_once(__DIR__ . '/../../runner.php');

class cli extends atoum\test
{
	public function test__construct()
	{
		$cli = new reports\cli();

		$decorator = new atoum\report\decorators\string();
		$decorator->addWriter(new atoum\writers\stdout());

		$this->assert
			->array($cli->getRunnerFields(atoum\runner::runStart))->isEqualTo(array(
					new report\fields\runner\version()
				)
			)
			->array($cli->getRunnerFields(atoum\runner::runStop))->isEqualTo(array(
					new atoum\report\fields\runner\tests\duration(),
					new atoum\report\fields\runner\tests\memory(),
					new atoum\report\fields\runner\tests\coverage(),
					new atoum\report\fields\runner\duration\string(),
					new atoum\report\fields\runner\result(),
					new atoum\report\fields\runner\failures(),
					new atoum\report\fields\runner\outputs(),
					new atoum\report\fields\runner\errors(),
					new atoum\report\fields\runner\exceptions()
				)
			)
			->array($cli->getTestFields(atoum\test::runStart))->isEqualTo(array(
					new atoum\report\fields\test\run(),
					new atoum\report\fields\test\event()
				)
			)
			->array($cli->getTestFields(atoum\test::beforeSetUp))->isEqualTo(array(
					new atoum\report\fields\test\event()
				)
			)
			->array($cli->getTestFields(atoum\test::afterSetUp))->isEqualTo(array(
					new atoum\report\fields\test\event()
				)
			)
			->array($cli->getTestFields(atoum\test::beforeTestMethod))->isEqualTo(array(
					new atoum\report\fields\test\event()
				)
			)
			->array($cli->getTestFields(atoum\test::success))->isEqualTo(array(
					new atoum\report\fields\test\event()
				)
			)
			->array($cli->getTestFields(atoum\test::fail))->isEqualTo(array(
					new atoum\report\fields\test\event()
				)
			)
			->array($cli->getTestFields(atoum\test::error))->isEqualTo(array(
					new atoum\report\fields\test\event()
				)
			)
			->array($cli->getTestFields(atoum\test::exception))->isEqualTo(array(
					new atoum\report\fields\test\event()
				)
			)
			->array($cli->getTestFields(atoum\test::afterTestMethod))->isEqualTo(array(
					new atoum\report\fields\test\event()
				)
			)
			->array($cli->getTestFields(atoum\test::beforeTearDown))->isEqualTo(array(
					new atoum\report\fields\test\event()
				)
			)
			->array($cli->getTestFields(atoum\test::afterTearDown))->isEqualTo(array(
					new atoum\report\fields\test\event()
				)
			)
			->array($cli->getTestFields(atoum\test::runStop))->isEqualTo(array(
					new atoum\report\fields\test\event(),
					new atoum\report\fields\test\duration(),
					new atoum\report\fields\test\memory()
				)
			)
			->array($cli->getDecorators())->isEqualTo(array(
					$decorator
				)
			)
		;
	}
}

?>
