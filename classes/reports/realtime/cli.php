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
		$topLevelColorizer = new colorizer('1;36');

		$secondLevelPrompt = new prompt('=> ', $topLevelColorizer);

		$failureColorizer = new colorizer('0;31');
		$failurePrompt = clone $secondLevelPrompt;
		$failurePrompt->setColorizer($failureColorizer);

		$errorColorizer = new colorizer('0;33');
		$errorPrompt = clone $secondLevelPrompt;
		$errorPrompt->setColorizer($errorColorizer);

		$exceptionColorizer = new colorizer('0;35');
		$exceptionPrompt = clone $secondLevelPrompt;
		$exceptionPrompt->setColorizer($exceptionColorizer);

		$this
			->addRunnerField(new runner\atoum\cli(
						$topLevelPrompt,
						$topLevelColorizer
					),
					array(atoum\runner::runStart)
				)
			->addRunnerField(new runner\php\path\cli(
						$topLevelPrompt,
						$topLevelColorizer
					),
					array(atoum\runner::runStart)
				)
			->addRunnerField(new runner\php\version\cli(
						$topLevelPrompt,
						$topLevelColorizer,
						$secondLevelPrompt
					),
					array(atoum\runner::runStart)
				)
			->addRunnerField(new runner\tests\duration\cli(
						$topLevelPrompt,
						$topLevelColorizer
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\tests\memory\cli(
						$topLevelPrompt,
						$topLevelColorizer
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\tests\coverage\cli(
						$topLevelPrompt,
						$secondLevelPrompt,
						new prompt('==> ', $topLevelColorizer),
						$topLevelColorizer
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\duration\cli(
						$topLevelPrompt,
						$topLevelColorizer
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
						$failureColorizer,
						$failurePrompt
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(
				new runner\outputs\cli(
						$topLevelPrompt,
						$topLevelColorizer,
						$secondLevelPrompt
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\errors\cli(
						$topLevelPrompt,
						$errorColorizer,
						$errorPrompt
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\exceptions\cli(
						$topLevelPrompt,
						$exceptionColorizer,
						$exceptionPrompt
					),
					array(atoum\runner::runStop)
				)
			->addTestField(new test\run\cli(
						$topLevelPrompt,
						$topLevelColorizer
					),
					array(atoum\test::runStart)
				)
			->addTestField(new test\event\cli())
			->addTestField(new test\duration\cli(
						$topLevelPrompt,
						$topLevelColorizer
					),
					array(atoum\test::runStop)
				)
			->addTestField(new test\memory\cli(
						$topLevelPrompt,
						$topLevelColorizer
					),
					array(atoum\test::runStop)
				)
		;
	}
}

?>
