<?php

namespace mageekguy\atoum\reports\realtime;

use
	mageekguy\atoum,
	mageekguy\atoum\reports\realtime,
	mageekguy\atoum\report\fields\runner,
	mageekguy\atoum\report\fields\test,
	mageekguy\atoum\cli\colorizer
;
use mageekguy\atoum\cli\prompt;

class santa extends realtime
{
	public function __construct()
	{
		parent::__construct();

		$defaultColorizer = new colorizer('1;31');

		$firstLevelPrompt = new prompt('> ');
		$secondLevelPrompt = new prompt('=> ', $defaultColorizer);
		$thirdLevelPrompt = new prompt('==> ', $defaultColorizer);

		$phpPathField = new runner\php\path\cli();
		$phpPathField
			->setPrompt($firstLevelPrompt)
			->setTitleColorizer($defaultColorizer)
		;

		$this->addField($phpPathField);

		$phpVersionField = new runner\php\version\cli();
		$phpVersionField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($defaultColorizer)
			->setVersionPrompt($secondLevelPrompt)
		;

		$this->addField($phpVersionField);

		$runnerTestsDurationField = new runner\tests\duration\cli();
		$runnerTestsDurationField
			->setPrompt($firstLevelPrompt)
			->setTitleColorizer($defaultColorizer)
		;

		$runnerTestsMemoryField = new runner\tests\memory\cli();
		$runnerTestsMemoryField
			->setPrompt($firstLevelPrompt)
			->setTitleColorizer($defaultColorizer)
		;

		$this->addField($runnerTestsMemoryField);

		$runnerTestsCoverageField = new runner\tests\coverage\cli();
		$runnerTestsCoverageField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($defaultColorizer)
			->setClassPrompt($secondLevelPrompt)
			->setMethodPrompt(new prompt('==> ', $defaultColorizer))
		;

		$this->addField($runnerTestsCoverageField);

		$runnerDurationField = new runner\duration\cli();
		$runnerDurationField
			->setPrompt($firstLevelPrompt)
			->setTitleColorizer($defaultColorizer)
		;

		$this->addField($runnerDurationField);

		$runnerResultField = new runner\result\cli();
		$runnerResultField
			->setSuccessColorizer(new colorizer('0;37', '42'))
			->setFailureColorizer(new colorizer('0;37', '41'))
		;

		$this->addField($runnerResultField);

		$this->addField($runnerTestsDurationField);

		$this->addField(new runner\event\santa());

		$failureColorizer = new colorizer('0;31');
		$failurePrompt = clone $secondLevelPrompt;
		$failurePrompt->setColorizer($failureColorizer);

		$runnerFailuresField = new runner\failures\cli();
		$runnerFailuresField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($failureColorizer)
			->setMethodPrompt($failurePrompt)
		;

		$this->addField($runnerFailuresField);

		$runnerOutputsField = new runner\outputs\cli();
		$runnerOutputsField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($defaultColorizer)
			->setMethodPrompt($secondLevelPrompt)
		;

		$this->addField($runnerOutputsField);

		$errorColorizer = new colorizer('0;33');
		$errorMethodPrompt = clone $secondLevelPrompt;
		$errorMethodPrompt->setColorizer($errorColorizer);
		$errorPrompt = clone $thirdLevelPrompt;
		$errorPrompt->setColorizer($errorColorizer);

		$runnerErrorsField = new runner\errors\cli();
		$runnerErrorsField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($errorColorizer)
			->setMethodPrompt($errorMethodPrompt)
			->setErrorPrompt($errorPrompt)
		;

		$this->addField($runnerErrorsField);

		$exceptionColorizer = new colorizer('0;35');
		$exceptionMethodPrompt = clone $secondLevelPrompt;
		$exceptionMethodPrompt->setColorizer($exceptionColorizer);
		$exceptionPrompt = clone $thirdLevelPrompt;
		$exceptionPrompt->setColorizer($exceptionColorizer);

		$runnerExceptionsField = new runner\exceptions\cli();
		$runnerExceptionsField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($exceptionColorizer)
			->setMethodPrompt($exceptionMethodPrompt)
			->setExceptionPrompt($exceptionPrompt)
		;

		$this->addField($runnerExceptionsField);

		$uncompletedTestColorizer = new colorizer('0;37');
		$uncompletedTestMethodPrompt = clone $secondLevelPrompt;
		$uncompletedTestMethodPrompt->setColorizer($uncompletedTestColorizer);
		$uncompletedTestOutputPrompt = clone $thirdLevelPrompt;
		$uncompletedTestOutputPrompt->setColorizer($uncompletedTestColorizer);

		$runnerUncompletedField = new runner\tests\uncompleted\cli();
		$runnerUncompletedField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($uncompletedTestColorizer)
			->setMethodPrompt($uncompletedTestMethodPrompt)
			->setOutputPrompt($uncompletedTestOutputPrompt)
		;

		$this->addField($runnerUncompletedField);

		$voidTestColorizer = new colorizer('0;34');
		$voidTestMethodPrompt = clone $secondLevelPrompt;
		$voidTestMethodPrompt->setColorizer($voidTestColorizer);

		$runnerVoidField = new runner\tests\void\cli();
		$runnerVoidField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($voidTestColorizer)
			->setMethodPrompt($voidTestMethodPrompt)
		;

		$this->addField($runnerVoidField);

		$skippedTestColorizer = new colorizer('0;90');
		$skippedTestMethodPrompt = clone $secondLevelPrompt;
		$skippedTestMethodPrompt->setColorizer($skippedTestColorizer);

		$runnerSkippedField = new runner\tests\skipped\cli();
		$runnerSkippedField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($skippedTestColorizer)
			->setMethodPrompt($skippedTestMethodPrompt)
		;

		$this->addField($runnerSkippedField);
	}
}
