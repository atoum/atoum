<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use
	\mageekguy\atoum,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\report\fields\test,
	\mageekguy\atoum\report\fields\runner,
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

		$runnerVersionField = new runner\version\cli(new prompt(''));
		$runnerPhpField = new runner\php\cli(new prompt(''), null, new prompt('   '));
		$runnerDurationField = new runner\duration\cli(new prompt(''));
		$runnerResultField = new runner\result\cli();
		$runnerFailuresField = new runner\failures\cli(new prompt(''), new colorizer(), new prompt('   ', new colorizer()), new colorizer());
		$runnerOutputsField = new runner\outputs\cli(null, null, new prompt('   '));
		$runnerErrorsField = new runner\errors\cli(new prompt(''), new colorizer(), new prompt('   '), new colorizer(), new prompt('      '), new colorizer());
		$runnerExceptionsField = new runner\exceptions\cli(new prompt(''), new colorizer(), new prompt('   '), new colorizer(), new prompt('      '), new colorizer());
		$runnerTestsDurationField = new runner\tests\duration\cli(null, '');
		$runnerTestsMemoryField = new runner\tests\memory\cli(new prompt(''));
		$runnerTestsCoverageField = new runner\tests\coverage\cli(null, '', '   ', '      ');

		$testRunField = new test\run\cli(null, '');
		$testDurationField = new test\duration\cli(null, '   ');
		$testMemoryField = new test\memory\cli(null, '   ');

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
