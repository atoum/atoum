<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	\mageekguy\atoum,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\reports,
	\mageekguy\atoum\report\fields\test,
	\mageekguy\atoum\report\fields\runner
;

class vim extends reports\asynchronous
{
	public function __construct(atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($locale, $adapter);

		$this
			->addRunnerField(new runner\atoum\cli(new prompt('> '), new colorizer()), array(atoum\runner::runStart))
			->addRunnerField(new runner\tests\duration\cli(
						new prompt('> ')
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\tests\memory\cli(
						new prompt('> ')
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\tests\coverage\cli(
						new prompt('> '),
						new prompt('=> '),
						new prompt('==> ')
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\duration\cli(
						new prompt('> ')
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\result\cli(
						new prompt('> ')
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\failures\cli(
						new prompt('> '),
						new colorizer(),
						new prompt('=> ', new colorizer()),
						new colorizer()
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(
				new runner\outputs\cli(
					new prompt('> '),
					null,
					new prompt('=> ')
				),
				array(atoum\runner::runStop)
			)
			->addRunnerField(new runner\errors\cli(
						new prompt('> '),
						new colorizer(),
						new prompt('=> '),
						new colorizer(),
						new prompt('==> '),
						new colorizer()
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\exceptions\cli(
						new prompt('> '),
						new colorizer(),
						new prompt('=> '),
						new colorizer(),
						new prompt('==> ', new colorizer()),
						new colorizer()
					),
					array(atoum\runner::runStop)
				)
			->addTestField(new test\run\cli(
						new prompt('> ')
					),
					array(atoum\test::runStart)
				)
			->addTestField(new test\duration\cli(), array(atoum\test::runStop))
			->addTestField(new test\memory\cli(), array(atoum\test::runStop))
		;
	}

	public function __toString()
	{
		return parent::__toString() . '/* vim: set ft=atoum: */';
	}
}

?>
