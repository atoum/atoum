<?php

namespace mageekguy\atoum\reports\realtime;

use
	mageekguy\atoum,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\reports\realtime,
	mageekguy\atoum\report\fields\test,
	mageekguy\atoum\report\fields\runner
;

class cli extends realtime
{
	public function __construct()
	{
		parent::__construct();

		$firstLevelPrompt = new prompt('> ');
		$firstLevelColorizer = new colorizer('1;36');

		$secondLevelPrompt = new prompt('=> ', $firstLevelColorizer);

		$thirdLevelPrompt = new prompt('==> ', $firstLevelColorizer);

		$failureColorizer = new colorizer('0;31');
		$failurePrompt = clone $secondLevelPrompt;
		$failurePrompt->setColorizer($failureColorizer);

		$errorColorizer = new colorizer('0;33');
		$errorMethodPrompt = clone $secondLevelPrompt;
		$errorMethodPrompt->setColorizer($errorColorizer);
		$errorPrompt = clone $thirdLevelPrompt;
		$errorPrompt->setColorizer($errorColorizer);

		$exceptionColorizer = new colorizer('0;35');
		$exceptionMethodPrompt = clone $secondLevelPrompt;
		$exceptionMethodPrompt->setColorizer($exceptionColorizer);
		$exceptionPrompt = clone $thirdLevelPrompt;
		$exceptionPrompt->setColorizer($exceptionColorizer);

		$uncompletedTestColorizer = new colorizer('0;37');
		$uncompletedTestMethodPrompt = clone $secondLevelPrompt;
		$uncompletedTestMethodPrompt->setColorizer($uncompletedTestColorizer);
		$uncompletedTestOutputPrompt = clone $thirdLevelPrompt;
		$uncompletedTestOutputPrompt->setColorizer($uncompletedTestColorizer);

		$this
			->addRunnerField(new runner\atoum\cli(
						$firstLevelPrompt,
						$firstLevelColorizer
					),
					array(atoum\runner::runStart)
				)
			->addRunnerField(new runner\php\path\cli(
						$firstLevelPrompt,
						$firstLevelColorizer
					),
					array(atoum\runner::runStart)
				)
			->addRunnerField(new runner\php\version\cli(
						$firstLevelPrompt,
						$firstLevelColorizer,
						$secondLevelPrompt
					),
					array(atoum\runner::runStart)
				)
			->addRunnerField(new runner\tests\duration\cli(
						$firstLevelPrompt,
						$firstLevelColorizer
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\tests\memory\cli(
						$firstLevelPrompt,
						$firstLevelColorizer
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\tests\coverage\cli(
						$firstLevelPrompt,
						$secondLevelPrompt,
						new prompt('==> ', $firstLevelColorizer),
						$firstLevelColorizer
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\duration\cli(
						$firstLevelPrompt,
						$firstLevelColorizer
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\result\cli(
						null,
						new colorizer('0;37', '42'),
						new colorizer('0;37', '41')
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\failures\cli(
						$firstLevelPrompt,
						$failureColorizer,
						$failurePrompt
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(
				new runner\outputs\cli(
						$firstLevelPrompt,
						$firstLevelColorizer,
						$secondLevelPrompt
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\errors\cli(
						$firstLevelPrompt,
						$errorColorizer,
						$errorMethodPrompt,
						null,
						$errorPrompt
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\exceptions\cli(
						$firstLevelPrompt,
						$exceptionColorizer,
						$exceptionMethodPrompt,
						null,
						$exceptionPrompt
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\tests\uncompleted\cli(
						$firstLevelPrompt,
						$uncompletedTestColorizer,
						$uncompletedTestMethodPrompt,
						null,
						$uncompletedTestOutputPrompt
					),
					array(atoum\runner::runStop)
				)
			->addTestField(new test\run\cli(
						$firstLevelPrompt,
						$firstLevelColorizer
					),
					array(atoum\test::runStart)
				)
			->addTestField(new test\event\cli())
			->addTestField(new test\duration\cli(
						$secondLevelPrompt
					),
					array(atoum\test::runStop)
				)
			->addTestField(new test\memory\cli(
						$secondLevelPrompt
					),
					array(atoum\test::runStop)
				)
		;
	}
}

?>
