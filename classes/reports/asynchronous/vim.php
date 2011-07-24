<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\reports,
	mageekguy\atoum\report\fields\test,
	mageekguy\atoum\report\fields\runner
;

class vim extends reports\asynchronous
{
	public function __construct(atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($locale, $adapter);

		$firstLevelPrompt = new prompt('> ');
		$secondLevelPrompt = new prompt('=> ');
		$thirdLevelPrompt = new prompt('==> ');

		$this
			->addRunnerField(new runner\atoum\cli($firstLevelPrompt, null), array(atoum\runner::runStart))
			->addRunnerField(new runner\php\path\cli(
						$firstLevelPrompt
					),
					array(atoum\runner::runStart)
				)
			->addRunnerField(new runner\php\version\cli(
						$firstLevelPrompt,
						null,
						$secondLevelPrompt
					),
					array(atoum\runner::runStart)
				)
			->addRunnerField(new runner\tests\duration\cli(
						$firstLevelPrompt
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\tests\memory\cli(
						$firstLevelPrompt
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\tests\coverage\cli(
						$firstLevelPrompt,
						$secondLevelPrompt,
						$thirdLevelPrompt
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\duration\cli(
						$firstLevelPrompt
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\result\cli(
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\failures\cli(
						$firstLevelPrompt,
						null,
						$secondLevelPrompt,
						null
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\errors\cli(
						$firstLevelPrompt,
						null,
						$secondLevelPrompt,
						null,
						$thirdLevelPrompt,
						null
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\exceptions\cli(
						$firstLevelPrompt,
						null,
						$secondLevelPrompt,
						null,
						$thirdLevelPrompt,
						null
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(
				new runner\outputs\cli(
					$firstLevelPrompt,
					null,
					$secondLevelPrompt
				),
				array(atoum\runner::runStop)
			)
			->addTestField(new test\run\cli(
						$firstLevelPrompt
					),
					array(atoum\test::runStart)
				)
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
