<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use
	\mageekguy\atoum,
	\mageekguy\atoum\cli\prompt,
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

		$runnerVersionField = new fields\runner\version\cli(new prompt(''));
		$runnerPhpField = new fields\runner\php\cli(new prompt(''), null, new prompt('   '));
		$runnerDurationField = new fields\runner\duration\cli(null, '');
		$runnerResultField = new fields\runner\result\cli();
		$runnerFailuresField = new fields\runner\failures\cli(null, '', '   ');
		$runnerOutputsField = new fields\runner\outputs\cli(null, '', '   ');
		$runnerErrorsField = new fields\runner\errors\cli(null, '', '   ', '      ');
		$runnerExceptionsField = new fields\runner\exceptions\cli(null, '', '   ', '      ');
		$runnerTestsDurationField = new fields\runner\tests\duration\cli(null, '');
		$runnerTestsMemoryField = new fields\runner\tests\memory\cli(null, '');
		$runnerTestsCoverageField = new fields\runner\tests\coverage\cli(null, '', '   ', '      ');

		$testRunField = new fields\test\run\cli(null, '');
		$testDurationField = new fields\test\duration\cli(null, '   ');
		$testMemoryField = new fields\test\memory\cli(null, '   ');

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
