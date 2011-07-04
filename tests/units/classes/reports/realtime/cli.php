<?php

namespace mageekguy\atoum\tests\units\reports\realtime;

use
	\mageekguy\atoum,
	\mageekguy\atoum\reports,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\report\fields
;

require_once(__DIR__ . '/../../../runner.php');

class cli extends atoum\test
{
	public function test__construct()
	{
		$report = new reports\realtime\cli();

		$this->assert
			->array($report->getRunnerFields(atoum\runner::runStart))->isEqualTo(array(
					new fields\runner\version\cli(),
					new fields\runner\php\cli()
				)
			)
			->array($report->getRunnerFields(atoum\runner::runStop))->isEqualTo(array(
					new fields\runner\tests\duration\cli(),
					new fields\runner\tests\memory\cli(),
					new fields\runner\tests\coverage\cli(),
					new fields\runner\duration\cli(
						new prompt('> '),
						new colorizer('1;36')
					),
					new fields\runner\result\cli(
						new colorizer('0;37', '42'),
						new colorizer('0;37', '41')
					),
					new fields\runner\failures\cli(
						new prompt('> '),
						new colorizer('0;31'),
						new prompt(
							'=> ',
							new colorizer('0;31')
						)
					),
					new fields\runner\outputs\cli(
						new prompt('> '),
						new colorizer('0;36'),
						new prompt(
							'=> ',
							new colorizer('0;36')
						)
					),
					new fields\runner\errors\cli(),
					new fields\runner\exceptions\cli()
				)
			)
			->array($report->getTestFields(atoum\test::runStart))->isEqualTo(array(
					new fields\test\run\cli(),
					new fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::beforeSetUp))->isEqualTo(array(
					new fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::afterSetUp))->isEqualTo(array(
					new fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::beforeTestMethod))->isEqualTo(array(
					new fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::success))->isEqualTo(array(
					new fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::fail))->isEqualTo(array(
					new fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::error))->isEqualTo(array(
					new fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::exception))->isEqualTo(array(
					new fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::afterTestMethod))->isEqualTo(array(
					new fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::beforeTearDown))->isEqualTo(array(
					new fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::afterTearDown))->isEqualTo(array(
					new fields\test\event\cli()
				)
			)
			->array($report->getTestFields(atoum\test::runStop))->isEqualTo(array(
					new fields\test\event\cli(),
					new fields\test\duration\cli(),
					new fields\test\memory\cli()
				)
			)
		;
	}
}

?>
