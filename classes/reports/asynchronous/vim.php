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
	public function __construct(atoum\factory $factory = null)
	{
		parent::__construct($factory);

		$firstLevelPrompt = new prompt('> ');
		$secondLevelPrompt = new prompt('=> ');
		$thirdLevelPrompt = new prompt('==> ');

		$this
			->addField(new runner\atoum\cli(
						$firstLevelPrompt
					)
				)
			->addField(new runner\php\path\cli(
						$firstLevelPrompt
					)
				)
			->addField(new runner\php\version\cli(
						$firstLevelPrompt,
						null,
						$secondLevelPrompt
					)
				)
			->addField(new runner\tests\duration\cli(
						$firstLevelPrompt
					)
				)
			->addField(new runner\tests\memory\cli(
						$firstLevelPrompt
					)
				)
			->addField(new runner\tests\coverage\cli(
						$firstLevelPrompt,
						$secondLevelPrompt,
						$thirdLevelPrompt
					)
				)
			->addField(new runner\duration\cli(
						$firstLevelPrompt
					)
				)
			->addField(new runner\result\cli(
					)
				)
			->addField(new runner\failures\cli(
						$firstLevelPrompt,
						null,
						$secondLevelPrompt
					)
				)
			->addField(new runner\errors\cli(
						$firstLevelPrompt,
						null,
						$secondLevelPrompt,
						null,
						$thirdLevelPrompt
					)
				)
			->addField(new runner\exceptions\cli(
						$firstLevelPrompt,
						null,
						$secondLevelPrompt,
						null,
						$thirdLevelPrompt
					)
				)
			->addField(new runner\tests\uncompleted\cli(
						$firstLevelPrompt,
						null,
						$secondLevelPrompt,
						null,
						$thirdLevelPrompt
					)
				)
			->addField(
				new runner\outputs\cli(
					$firstLevelPrompt,
					null,
					$secondLevelPrompt
				)
			)
			->addField(new test\run\cli(
					$firstLevelPrompt
				)
			)
			->addField(new test\duration\cli(
					$secondLevelPrompt
				)
			)
			->addField(new test\memory\cli(
					$secondLevelPrompt
				)
			)
		;
	}
}
