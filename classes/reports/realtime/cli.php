<?php

namespace mageekguy\atoum\reports\realtime;

use
	\mageekguy\atoum,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\reports\realtime,
	\mageekguy\atoum\report\fields\test,
	\mageekguy\atoum\report\fields\runner
;

class cli extends realtime
{
	public function __construct()
	{
		parent::__construct();

		$this
			->addRunnerField(new runner\version\cli(
						new prompt('> '),
						new colorizer('1;36')
					),
					array(atoum\runner::runStart)
				)
			->addRunnerField(new runner\php\cli(
						new prompt('> '),
						new colorizer('1;36'),
						new prompt('=> ')
					),
					array(atoum\runner::runStart)
				)
			->addRunnerField(new runner\tests\duration\cli(), array(atoum\runner::runStop))
			->addRunnerField(new runner\tests\memory\cli(), array(atoum\runner::runStop))
			->addRunnerField(new runner\tests\coverage\cli(), array(atoum\runner::runStop))
			->addRunnerField(new runner\duration\cli(
						new prompt('> '),
						new colorizer('1;36')
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\result\cli(
						new prompt('> '),
						new colorizer('0;37', '42'),
						new colorizer('0;37', '41')
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\failures\cli(
						new prompt('> '),
						new colorizer('0;31'),
						new prompt(
							'=> ',
							new colorizer('0;31')
						)
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(
				new runner\outputs\cli(
						new prompt('> '),
						new colorizer('0;36'),
						new prompt(
							'=> ',
							new colorizer('0;36')
						)
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\errors\cli(), array(atoum\runner::runStop))
			->addRunnerField(new runner\exceptions\cli(), array(atoum\runner::runStop))
			->addTestField(new test\run\cli(), array(atoum\test::runStart))
			->addTestField(new test\event\cli())
			->addTestField(new test\duration\cli(), array(atoum\test::runStop))
			->addTestField(new test\memory\cli(), array(atoum\test::runStop))
		;
	}
}

?>
