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
					new report\fields\runner\version\string()
				)
			)
			->array($cli->getRunnerFields(atoum\runner::runStop))->isEqualTo(array(
					new atoum\report\fields\runner\tests\duration\string(),
					new atoum\report\fields\runner\tests\memory\string(),
					new atoum\report\fields\runner\tests\coverage\string(),
					new atoum\report\fields\runner\duration\string(),
					new atoum\report\fields\runner\result\string(),
					new atoum\report\fields\runner\failures\string(),
					new atoum\report\fields\runner\outputs\string(),
					new atoum\report\fields\runner\errors\string(),
					new atoum\report\fields\runner\exceptions\string()
				)
			)
			->array($cli->getTestFields(atoum\test::runStart))->isEqualTo(array(
					new atoum\report\fields\test\run\string(),
					new atoum\report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::beforeSetUp))->isEqualTo(array(
					new atoum\report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::afterSetUp))->isEqualTo(array(
					new atoum\report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::beforeTestMethod))->isEqualTo(array(
					new atoum\report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::success))->isEqualTo(array(
					new atoum\report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::fail))->isEqualTo(array(
					new atoum\report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::error))->isEqualTo(array(
					new atoum\report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::exception))->isEqualTo(array(
					new atoum\report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::afterTestMethod))->isEqualTo(array(
					new atoum\report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::beforeTearDown))->isEqualTo(array(
					new atoum\report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::afterTearDown))->isEqualTo(array(
					new atoum\report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::runStop))->isEqualTo(array(
					new atoum\report\fields\test\event\string(),
					new atoum\report\fields\test\duration\string(),
					new atoum\report\fields\test\memory\string()
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
