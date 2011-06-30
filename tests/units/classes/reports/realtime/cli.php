<?php

namespace mageekguy\atoum\tests\units\reports\realtime;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report,
	 \mageekguy\atoum\reports\realtime as reports
;

require_once(__DIR__ . '/../../../runner.php');

class cli extends atoum\test
{
	public function test__construct()
	{
		$report = new reports\cli();

		$this->assert
			->array($report->getRunnerFields(atoum\runner::runStart))->isEqualTo(array(
					new report\fields\runner\version\cli(),
					new report\fields\runner\php\cli()
				)
			)
			->array($report->getRunnerFields(atoum\runner::runStop))->isEqualTo(array(
					new report\fields\runner\tests\duration\cli(),
					new report\fields\runner\tests\memory\cli(),
					new report\fields\runner\tests\coverage\cli(),
					new report\fields\runner\duration\cli(),
					new report\fields\runner\result\cli(),
					new report\fields\runner\failures\cli(),
					new report\fields\runner\outputs\cli(),
					new report\fields\runner\errors\cli(),
					new report\fields\runner\exceptions\cli()
				)
			)
			->array($report->getTestFields(atoum\test::runStart))->isEqualTo(array(
					new report\fields\test\run\cli(),
					new report\fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::beforeSetUp))->isEqualTo(array(
					new report\fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::afterSetUp))->isEqualTo(array(
					new report\fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::beforeTestMethod))->isEqualTo(array(
					new report\fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::success))->isEqualTo(array(
					new report\fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::fail))->isEqualTo(array(
					new report\fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::error))->isEqualTo(array(
					new report\fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::exception))->isEqualTo(array(
					new report\fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::afterTestMethod))->isEqualTo(array(
					new report\fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::beforeTearDown))->isEqualTo(array(
					new report\fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::afterTearDown))->isEqualTo(array(
					new report\fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::runStop))->isEqualTo(array(
					new report\fields\test\event\cli(),
					new report\fields\test\duration\cli(),
					new report\fields\test\memory\cli()
				)
			)
		;
	}
}

?>
