<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use \mageekguy\atoum;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields;
use \mageekguy\atoum\reports\asynchronous as reports;

require_once(__DIR__ . '/../../../runner.php');

class builder extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->class('\mageekguy\atoum\reports\asynchronous\builder')->isSubClassOf('\mageekguy\atoum\reports\asynchronous')
		;
	}

	public function test__construct()
	{
		$report = new reports\builder();

		$testRunField = new fields\test\run\string('Test class %s:', '');

		$this->assert
			->array($report->getRunnerFields(atoum\runner::runStart))->isEqualTo(array(
					new report\fields\runner\version\string($report->getLocale()),
					new report\fields\runner\php\string()
				)
			)
			->array($report->getRunnerFields(atoum\runner::runStop))->isEqualTo(array(
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
			->array($report->getTestFields(atoum\test::runStart))->isEqualTo(array(
					$testRunField,
				)
			)
			->array($report->getTestFields(atoum\test::beforeSetUp))->isEmpty()
			->array($report->getTestFields(atoum\test::afterSetUp))->isEmpty()
			->array($report->getTestFields(atoum\test::beforeTestMethod))->isEmpty()
			->array($report->getTestFields(atoum\test::success))->isEmpty()
			->array($report->getTestFields(atoum\test::fail))->isEmpty()
			->array($report->getTestFields(atoum\test::error))->isEmpty()
			->array($report->getTestFields(atoum\test::exception))->isEmpty()
			->array($report->getTestFields(atoum\test::afterTestMethod))->isEmpty()
			->array($report->getTestFields(atoum\test::beforeTearDown))->isEmpty()
			->array($report->getTestFields(atoum\test::afterTearDown))->isEmpty()
			->array($report->getTestFields(atoum\test::runStop))->isEqualTo(array(
					new report\fields\test\duration\string(),
					new report\fields\test\memory\string()
				)
			)
		;
	}
}

?>
