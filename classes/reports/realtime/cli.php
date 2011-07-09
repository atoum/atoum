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

		$topLevelPrompt = new prompt('> ');
		$topLevelColor = new colorizer('1;36');
		$failureColor = new colorizer('0;31');
		$errorColor = new colorizer('0;33');
		$exceptionColor = new colorizer('0;35');

		$this
			->addRunnerField(new runner\atoum\cli(
						$topLevelPrompt,
						$topLevelColor
					),
					array(atoum\runner::runStart)
				)
			->addRunnerField(new runner\php\path\cli(
						$topLevelPrompt,
						$topLevelColor
					),
					array(atoum\runner::runStart)
				)
			->addRunnerField(new runner\php\version\cli(
						$topLevelPrompt,
						$topLevelColor,
						new prompt('=> ', $topLevelColor)
					),
					array(atoum\runner::runStart)
				)
			->addRunnerField(new runner\tests\duration\cli(
						$topLevelPrompt,
						$topLevelColor
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\tests\memory\cli(
						$topLevelPrompt,
						$topLevelColor
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\tests\coverage\cli(
						$topLevelPrompt,
						new prompt('=> ', $topLevelColor),
						new prompt('==> ', $topLevelColor),
						$topLevelColor
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\duration\cli(
						$topLevelPrompt,
						$topLevelColor
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\result\cli(
						$topLevelPrompt,
						new colorizer('0;37', '42'),
						new colorizer('0;37', '41')
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\failures\cli(
						$topLevelPrompt,
						$failureColor,
						new prompt(
							'=> ',
							$failureColor
						)
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(
				new runner\outputs\cli(
						$topLevelPrompt,
						$topLevelColor,
						new prompt(
							'=> ',
							$topLevelColor
						)
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\errors\cli(
						$topLevelPrompt,
						$errorColor,
						new prompt(
							'=> ',
							$errorColor
						)
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\exceptions\cli(
						$topLevelPrompt,
						$exceptionColor,
						new prompt(
							'=> ',
							$exceptionColor
						)
					),
					array(atoum\runner::runStop)
				)
			->addTestField(new test\run\cli(
						$topLevelPrompt,
						$topLevelColor
					),
					array(atoum\test::runStart)
				)
			->addTestField(new test\event\cli())
			->addTestField(new test\duration\cli(
						$topLevelPrompt,
						$topLevelColor
					),
					array(atoum\test::runStop)
				)
			->addTestField(new test\memory\cli(
						$topLevelPrompt,
						$topLevelColor
					),
					array(atoum\test::runStop)
				)
		;
	}
}

?>
