<?php

namespace mageekguy\atoum\tests\units\reports\realtime;

use
	mageekguy\atoum,
	mageekguy\atoum\reports,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields
;

require_once(__DIR__ . '/../../../runner.php');

class cli extends atoum\test
{
	public function test__construct()
	{
		$report = new reports\realtime\cli();

		$this->assert
			->array($report->getRunnerFields(atoum\runner::runStart))->isEqualTo(array(
					new fields\runner\atoum\cli(
						new prompt('> '),
						new colorizer('1;36;38;1;117')
					),
					new fields\runner\php\path\cli(
						new prompt('> '),
						new colorizer('1;36;38;1;117')
					),
					new fields\runner\php\version\cli(
						new prompt('> '),
						new colorizer('1;36;38;1;117'),
						new prompt('=> ', new colorizer('1;36;38;1;117'))
					)
				)
			)
			->array($report->getRunnerFields(atoum\runner::runStop))->isEqualTo(array(
					new fields\runner\tests\duration\cli(
						new prompt('> '),
						new colorizer('1;36;38;1;117')
					),
					new fields\runner\tests\memory\cli(
						new prompt('> '),
						new colorizer('1;36;38;1;117')
					),
					new fields\runner\tests\coverage\cli(
						new prompt('> '),
						new prompt('=> ', new colorizer('1;36;38;1;117')),
						new prompt('==> ', new colorizer('1;36;38;1;117')),
						new colorizer('1;36;38;1;117')
					),
					new fields\runner\duration\cli(
						new prompt('> '),
						new colorizer('1;36;38;1;117')
					),
					new fields\runner\result\cli(
						null,
						new colorizer('1;37', '42;48;1;136'),
						new colorizer('1;37', '41;48;1;124')
					),
					new fields\runner\failures\cli(
						new prompt('> '),
						new colorizer('0;31;38;1;160'),
						new prompt('=> ', new colorizer('0;31;38;1;160'))
					),
					new fields\runner\outputs\cli(
						new prompt('> '),
						new colorizer('1;36;38;1;117'),
						new prompt('=> ', new colorizer('1;36;38;1;117'))
					),
					new fields\runner\errors\cli(
						new prompt('> '),
						new colorizer('0;33;38;1;220'),
						new prompt('=> ', new colorizer('0;33;38;1;220')),
						null,
						new prompt('==> ', new colorizer('0;33;38;1;220'))
					),
					new fields\runner\exceptions\cli(
						new prompt('> '),
						new colorizer('0;35;38;1;135'),
						new prompt('=> ', new colorizer('0;35;38;1;135')),
						null,
						new prompt('==> ', new colorizer('0;35;38;1;135'))
					)
				)
			)
			->array($report->getTestFields(atoum\test::runStart))->isEqualTo(array(
					new fields\test\run\cli(
						new prompt('> '),
						new colorizer('1;36;38;1;117')
					),
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
					new fields\test\duration\cli(
						new prompt('=> ', new colorizer('1;36;38;1;117'))
					),
					new fields\test\memory\cli(
						new prompt('=> ', new colorizer('1;36;38;1;117'))
					)
				)
			)
		;
	}
}

?>
