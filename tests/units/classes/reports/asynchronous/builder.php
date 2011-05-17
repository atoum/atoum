<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields,
	\mageekguy\atoum\reports\asynchronous as reports
;

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

		$runnerVersionField = new fields\runner\version\string(null, '');
		$runnerPhpField = new fields\runner\php\string(null, '', '   ');
		$runnerDurationField = new fields\runner\duration\string(null, '');
		$runnerResultField = new fields\runner\result\string(null, '');
		$runnerFailuresField = new fields\runner\failures\string(null, '', '   ');
		$runnerOutputsField = new fields\runner\outputs\string(null, '', '   ');
		$runnerErrorsField = new fields\runner\errors\string(null, '', '   ', '      ');
		$runnerExceptionsField = new fields\runner\exceptions\string(null, '', '   ', '      ');
		$runnerTestsDurationField = new fields\runner\tests\duration\string(null, '');
		$runnerTestsMemoryField = new fields\runner\tests\memory\string(null, '');
		$runnerTestsCoverageField = new fields\runner\tests\coverage\string(null, '', '   ', '      ');

		$testRunField = new fields\test\run\string(null, '');
		$testDurationField = new fields\test\duration\string(null, '   ');
		$testMemoryField = new fields\test\memory\string(null, '   ');

		$this->assert
			->array($report->getRunnerFields(atoum\runner::runStart))->isEqualTo(array(
					$runnerVersionField,
					$runnerPhpField
				)
			)
			->array($report->getRunnerFields(atoum\runner::runStop))->isEqualTo(array(
					$runnerTestsDurationField,
					$runnerTestsMemoryField,
					$runnerTestsCoverageField,
					$runnerDurationField,
					$runnerResultField,
					$runnerFailuresField,
					$runnerOutputsField,
					$runnerErrorsField,
					$runnerExceptionsField
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
					$testDurationField,
					$testMemoryField
				)
			)
		;

		$report = new reports\builder($locale = new atoum\locale(), $adapter = new atoum\adapter());

		$this->assert
			->object($report->getLocale())->isIdenticalTo($locale)
			->object($report->getAdapter())->isIdenticalTo($adapter)
		;
	}
}

?>
