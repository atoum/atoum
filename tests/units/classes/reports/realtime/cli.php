<?php

namespace mageekguy\atoum\tests\units\reports\realtime;

use \mageekguy\atoum;
use \mageekguy\atoum\report;
use \mageekguy\atoum\reports\realtime;

require_once(__DIR__ . '/../../../runner.php');

class cli extends atoum\test
{
	public function test__construct()
	{
		$cli = new realtime\cli();

		$this->assert
			->array($cli->getRunnerFields(atoum\runner::runStart))->isEqualTo(array(
					new report\fields\runner\version\string()
				)
			)
			->array($cli->getRunnerFields(atoum\runner::runStop))->isEqualTo(array(
					new report\fields\runner\tests\duration\string(),
					new report\fields\runner\tests\memory\string(),
					new report\fields\runner\tests\coverage\string(),
					new report\fields\runner\duration\string(),
					new report\fields\runner\result\string(),
					new report\fields\runner\failures\string(),
					new report\fields\runner\outputs\string(),
					new report\fields\runner\errors\string(),
					new report\fields\runner\exceptions\string()
				)
			)
			->array($cli->getTestFields(atoum\test::runStart))->isEqualTo(array(
					new report\fields\test\run\string(),
					new report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::beforeSetUp))->isEqualTo(array(
					new report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::afterSetUp))->isEqualTo(array(
					new report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::beforeTestMethod))->isEqualTo(array(
					new report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::success))->isEqualTo(array(
					new report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::fail))->isEqualTo(array(
					new report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::error))->isEqualTo(array(
					new report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::exception))->isEqualTo(array(
					new report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::afterTestMethod))->isEqualTo(array(
					new report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::beforeTearDown))->isEqualTo(array(
					new report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::afterTearDown))->isEqualTo(array(
					new report\fields\test\event\string()
				)
			)
			->array($cli->getTestFields(atoum\test::runStop))->isEqualTo(array(
					new report\fields\test\event\string(),
					new report\fields\test\duration\string(),
					new report\fields\test\memory\string()
				)
			)
		;
	}
}

?>
