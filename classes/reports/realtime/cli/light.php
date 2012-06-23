<?php

namespace mageekguy\atoum\reports\realtime\cli;

use
	mageekguy\atoum,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\reports\realtime,
	mageekguy\atoum\report\fields\test,
	mageekguy\atoum\report\fields\runner
;

class light extends realtime
{
	public function __construct(atoum\factory $factory = null)
	{
		parent::__construct($factory);

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
			->addField(new runner\event\cli())
			->addField(new runner\result\cli(
						null,
						new colorizer('0;37', '42'),
						new colorizer('0;37', '41')
					)
				)
			->addField(new runner\failures\cli(
						$firstLevelPrompt,
						$failureColorizer,
						$failurePrompt
					)
				)
			->addField(
				new runner\outputs\cli(
						$firstLevelPrompt,
						$firstLevelColorizer,
						$secondLevelPrompt
					)
				)
			->addField(new runner\errors\cli(
						$firstLevelPrompt,
						$errorColorizer,
						$errorMethodPrompt,
						null,
						$errorPrompt
					)
				)
			->addField(new runner\exceptions\cli(
						$firstLevelPrompt,
						$exceptionColorizer,
						$exceptionMethodPrompt,
						null,
						$exceptionPrompt
					)
				)
			->addField(new runner\tests\uncompleted\cli(
						$firstLevelPrompt,
						$uncompletedTestColorizer,
						$uncompletedTestMethodPrompt,
						null,
						$uncompletedTestOutputPrompt
					)
				)
		;
	}
}
