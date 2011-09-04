<?php

namespace mageekguy\atoum\tests\units\reports\realtime;

use
	mageekguy\atoum,
	mageekguy\atoum\reports,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields
;

require_once __DIR__ . '/../../../runner.php';

class cli extends atoum\test
{
	public function test__construct()
	{
		$report = new reports\realtime\cli();

		$this->assert
			->array($report->getRunnerFields(atoum\runner::runStart))->isEqualTo(array(
					new fields\runner\atoum\cli(
						new prompt('> '),
						new colorizer('1;36')
					),
					new fields\runner\php\path\cli(
						new prompt('> '),
						new colorizer('1;36')
					),
					new fields\runner\php\version\cli(
						new prompt('> '),
						new colorizer('1;36'),
						new prompt('=> ', new colorizer('1;36'))
					)
				)
			)
			->array($report->getRunnerFields(atoum\runner::runStop))->isEqualTo(array(
					new fields\runner\tests\duration\cli(
						new prompt('> '),
						new colorizer('1;36')
					),
					new fields\runner\tests\memory\cli(
						new prompt('> '),
						new colorizer('1;36')
					),
					new fields\runner\tests\coverage\cli(
						new prompt('> '),
						new prompt('=> ', new colorizer('1;36')),
						new prompt('==> ', new colorizer('1;36')),
						new colorizer('1;36')
					),
					new fields\runner\duration\cli(
						new prompt('> '),
						new colorizer('1;36')
					),
					new fields\runner\result\cli(
						null,
						new colorizer('0;37', '42'),
						new colorizer('0;37', '41')
					),
					new fields\runner\failures\cli(
						new prompt('> '),
						new colorizer('0;31'),
						new prompt('=> ', new colorizer('0;31'))
					),
					new fields\runner\outputs\cli(
						new prompt('> '),
						new colorizer('1;36'),
						new prompt('=> ', new colorizer('1;36'))
					),
					new fields\runner\errors\cli(
						new prompt('> '),
						new colorizer('0;33'),
						new prompt('=> ', new colorizer('0;33')),
						null,
						new prompt('==> ', new colorizer('0;33'))
					),
					new fields\runner\exceptions\cli(
						new prompt('> '),
						new colorizer('0;35'),
						new prompt('=> ', new colorizer('0;35')),
						null,
						new prompt('==> ', new colorizer('0;35'))
					)
				)
			)
			->array($report->getTestFields(atoum\test::runStart))->isEqualTo(array(
					new fields\test\run\cli(
						new prompt('> '),
						new colorizer('1;36')
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
						new prompt('=> ', new colorizer('1;36'))
					),
					new fields\test\memory\cli(
						new prompt('=> ', new colorizer('1;36'))
					)
				)
			)
		;
	}
}

?>
