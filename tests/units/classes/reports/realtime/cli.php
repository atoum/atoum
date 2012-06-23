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
		$this
			->if($report = new reports\realtime\cli())
			->then
				->object($report->getFactory())->isInstanceOf('mageekguy\atoum\factory')
				->object($report->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($report->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->array($report->getFields())->isEqualTo(array(
						new fields\runner\php\path\cli(
							new prompt('> '),
							new colorizer('1;36')
						),
						new fields\runner\php\version\cli(
							new prompt('> '),
							new colorizer('1;36'),
							new prompt('=> ', new colorizer('1;36'))
						),
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
						),
						new fields\runner\tests\uncompleted\cli(
							new prompt('> '),
							new colorizer('0;37'),
							new prompt('=> ', new colorizer('0;37')),
							null,
							new prompt('==> ', new colorizer('0;37'))
						),
						new fields\test\run\cli(
							new prompt('> '),
							new colorizer('1;36')
						),
						new fields\test\event\cli(),
						new fields\test\duration\cli(
							new prompt('=> ', new colorizer('1;36'))
						),
						new fields\test\memory\cli(
							new prompt('=> ', new colorizer('1;36'))
						)
					)
				)
			->if($factory = new atoum\factory())
			->and($factory['mageekguy\atoum\locale'] = $locale = new atoum\locale())
			->and($factory['mageekguy\atoum\adapter'] = $adapter = new atoum\adapter())
			->and($report = new reports\realtime\cli($factory))
			->then
				->object($report->getFactory())->isIdenticalTo($factory)
				->object($report->getAdapter())->isIdenticalTo($adapter)
				->object($report->getLocale())->isIdenticalTo($locale)
				->array($report->getFields())->isEqualTo(array(
						new fields\runner\php\path\cli(
							new prompt('> '),
							new colorizer('1;36')
						),
						new fields\runner\php\version\cli(
							new prompt('> '),
							new colorizer('1;36'),
							new prompt('=> ', new colorizer('1;36'))
						),
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
						),
						new fields\runner\tests\uncompleted\cli(
							new prompt('> '),
							new colorizer('0;37'),
							new prompt('=> ', new colorizer('0;37')),
							null,
							new prompt('==> ', new colorizer('0;37'))
						),
						new fields\test\run\cli(
							new prompt('> '),
							new colorizer('1;36')
						),
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
