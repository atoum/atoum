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
	const defaultRunnerDurationPrompt = '> ';
	const defaultRunnerDurationTitleColor = '1;36';

	const defaultOutputTitlePrompt = '> ';
	const defaultOutputMethodPrompt = '=> ';
	const defaultOutputColor = '0;36';

	public function __construct()
	{
		parent::__construct();

		$this
			->addRunnerField(new runner\version\cli(), array(atoum\runner::runStart))
			->addRunnerField(new runner\php\cli(), array(atoum\runner::runStart))
			->addRunnerField(new runner\tests\duration\cli(), array(atoum\runner::runStop))
			->addRunnerField(new runner\tests\memory\cli(), array(atoum\runner::runStop))
			->addRunnerField(new runner\tests\coverage\cli(), array(atoum\runner::runStop))
			->addRunnerField(new runner\duration\cli(
						new prompt(self::defaultRunnerDurationPrompt),
						new colorizer(self::defaultRunnerDurationTitleColor)
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\result\cli(new atoum\cli\colorizer('0;37', '42'), new atoum\cli\colorizer('0;37', '41')), array(atoum\runner::runStop))
			->addRunnerField(new runner\failures\cli(), array(atoum\runner::runStop))
			->addRunnerField(
				new runner\outputs\cli(
					new prompt(self::defaultOutputTitlePrompt),
					new colorizer(self::defaultOutputColor),
					new prompt(self::defaultOutputMethodPrompt,
						new colorizer(self::defaultOutputColor)
					),
					new colorizer(self::defaultOutputColor)
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
