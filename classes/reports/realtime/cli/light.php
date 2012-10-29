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
	public function __construct()
	{
		parent::__construct();

		$firstLevelColorizer = new colorizer('1;36');

		$firstLevelPrompt = new prompt('> ');
		$secondLevelPrompt = new prompt('=> ', $firstLevelColorizer);
		$thirdLevelPrompt = new prompt('==> ', $firstLevelColorizer);

		$this->addField(new runner\event\cli());

		$resultField = new runner\result\cli();
		$resultField
			->setSuccessColorizer(new colorizer('0;37', '42'))
			->setFailureColorizer(new colorizer('0;37', '41'))
		;

		$this->addField($resultField);

		$failureColorizer = new colorizer('0;31');
		$failurePrompt = clone $secondLevelPrompt;
		$failurePrompt->setColorizer($failureColorizer);

		$failuresField = new runner\failures\cli();
		$failuresField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($firstLevelColorizer)
			->setMethodPrompt($secondLevelPrompt)
		;

		$this->addField($failuresField);

		$outputsField = new runner\outputs\cli();
		$outputsField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($firstLevelColorizer)
			->setMethodPrompt($secondLevelPrompt)
		;

		$this->addField($outputsField);

		$errorColorizer = new colorizer('0;33');
		$errorMethodPrompt = clone $secondLevelPrompt;
		$errorMethodPrompt->setColorizer($errorColorizer);
		$errorPrompt = clone $thirdLevelPrompt;
		$errorPrompt->setColorizer($errorColorizer);

		$errorsField = new runner\errors\cli();
		$errorsField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($errorColorizer)
			->setMethodPrompt($errorMethodPrompt)
			->setErrorPrompt($errorPrompt)
		;

		$this->addField($errorsField);

		$exceptionColorizer = new colorizer('0;35');
		$exceptionMethodPrompt = clone $secondLevelPrompt;
		$exceptionMethodPrompt->setColorizer($exceptionColorizer);
		$exceptionPrompt = clone $thirdLevelPrompt;
		$exceptionPrompt->setColorizer($exceptionColorizer);

		$exceptionsField = new runner\exceptions\cli();
		$exceptionsField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($exceptionColorizer)
			->setMethodPrompt($exceptionMethodPrompt)
			->setExceptionPrompt($exceptionPrompt)
		;

		$this->addField($exceptionsField);

		$uncompletedTestColorizer = new colorizer('0;37');
		$uncompletedTestMethodPrompt = clone $secondLevelPrompt;
		$uncompletedTestMethodPrompt->setColorizer($uncompletedTestColorizer);
		$uncompletedTestOutputPrompt = clone $thirdLevelPrompt;
		$uncompletedTestOutputPrompt->setColorizer($uncompletedTestColorizer);

		$uncompletedTestField = new runner\tests\uncompleted\cli();
		$uncompletedTestField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($uncompletedTestColorizer)
			->setMethodPrompt($uncompletedTestMethodPrompt)
			->setOutputPrompt($uncompletedTestOutputPrompt)
		;

		$this->addField($uncompletedTestField);

		$voidTestColorizer = new colorizer('0;34');
		$voidTestMethodPrompt = clone $secondLevelPrompt;
		$voidTestMethodPrompt->setColorizer($voidTestColorizer);
		$voidTestOutputPrompt = clone $thirdLevelPrompt;
		$voidTestOutputPrompt->setColorizer($voidTestColorizer);

		$voidTestField = new runner\tests\void\cli();
		$voidTestField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($voidTestColorizer)
			->setMethodPrompt($voidTestMethodPrompt)
		;

		$this->addField($voidTestField);

		$skippedTestColorizer = new colorizer('0;90');
		$skippedTestMethodPrompt = clone $secondLevelPrompt;
		$skippedTestMethodPrompt->setColorizer($skippedTestColorizer);

		$skippedTestField = new runner\tests\skipped\cli();
		$skippedTestField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($skippedTestColorizer)
			->setMethodPrompt($skippedTestMethodPrompt)
		;

		$this->addField($skippedTestField);
	}
}
