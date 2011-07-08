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

		$testDurationField = new test\duration\cli(null, '   ');
		$testMemoryField = new test\memory\cli(null, '   ');

		$this->assert
			->array($report->getRunnerFields(atoum\runner::runStart))->isEqualTo(array(
					new runner\atoum\cli(),
					new runner\php\path\cli(
					),
					new runner\php\version\cli(
						null,
						null,
						new prompt('   ')
					)
				)
			)
			->array($report->getRunnerFields(atoum\runner::runStop))->isEqualTo(array(
					new runner\duration\cli(),
					new runner\result\cli(),
					new runner\failures\cli(
						null,
						null,
						new prompt('   ')
					),
					new runner\outputs\cli(
						null,
						null,
						new prompt('   ')
					),
					new runner\errors\cli(
						null,
						null,
						new prompt('   '),
						null,
						new prompt('      ')
					),
					new runner\exceptions\cli(
						null,
						null,
						new prompt('   '),
						null,
						new prompt('      ')
					),
					new runner\tests\duration\cli(),
					new runner\tests\memory\cli(),
					new runner\tests\coverage\cli(null, new prompt('   '), new prompt('      '))
				)
			)
			->array($report->getTestFields(atoum\test::runStart))->isEqualTo(array(
					new test\run\cli()
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
