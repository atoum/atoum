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
	public function __construct()
	{
		parent::__construct();

		$firstLevelPrompt = new prompt('> ');
		$secondLevelPrompt = new prompt('=> ');
		$thirdLevelPrompt = new prompt('==> ');

		$phpPathField = new runner\php\path\cli();
		$phpPathField->setPrompt($firstLevelPrompt);

		$this->addField($phpPathField);

		$phpVersionField = new runner\php\version\cli();
		$phpVersionField
			->setTitlePrompt($firstLevelPrompt)
			->setVersionPrompt($secondLevelPrompt)
		;

		$this->addField($phpVersionField);

		$testsDurationField = new runner\tests\duration\cli();
		$testsDurationField->setPrompt($firstLevelPrompt);

		$this->addField($testsDurationField);

		$memoryField = new runner\tests\memory\cli();
		$memoryField->setPrompt($firstLevelPrompt);

		$this->addField($memoryField);

		$coverageField = new runner\tests\coverage\cli();
		$coverageField
			->setTitlePrompt($firstLevelPrompt)
			->setClassPrompt($secondLevelPrompt)
			->setMethodPrompt($thirdLevelPrompt)
		;

		$runnerDurationField = new runner\duration\cli();
		$runnerDurationField->setPrompt($firstLevelPrompt);

		$this->addField($runnerDurationField);

		$resultField = new runner\result\cli();

		$this->addField($resultField);

		$failuresField = new runner\failures\cli();
		$failuresField
			->setTitlePrompt($firstLevelPrompt)
			->setMethodPrompt($secondLevelPrompt)
		;

		$this->addfield($failuresField);

		$errorsField = new runner\errors\cli();
		$errorsField
			->setTitlePrompt($firstLevelPrompt)
			->setMethodPrompt($secondLevelPrompt)
			->setErrorPrompt($thirdLevelPrompt)
		;

		$this->addField($errorsField);

		$exceptionsField = new runner\exceptions\cli();
		$exceptionsField
			->setTitlePrompt($firstLevelPrompt)
			->setMethodPrompt($secondLevelPrompt)
			->setExceptionPrompt($thirdLevelPrompt)
		;

		$this->addField($exceptionsField);

		$uncompletedField = new runner\tests\uncompleted\cli();
		$uncompletedField
			->setTitlePrompt($firstLevelPrompt)
			->setMethodPrompt($secondLevelPrompt)
			->setOutputPrompt($thirdLevelPrompt)
		;

		$this->addField($uncompletedField);

		$voidField = new runner\tests\void\cli();
		$voidField
			->setTitlePrompt($firstLevelPrompt)
			->setMethodPrompt($secondLevelPrompt)
		;

		$this->addField($voidField);

		$skippedField = new runner\tests\skipped\cli();
		$skippedField
			->setTitlePrompt($firstLevelPrompt)
			->setMethodPrompt($secondLevelPrompt)
		;

		$this->addField($skippedField);

		$outputField = new runner\outputs\cli();
		$outputField
			->setTitlePrompt($firstLevelPrompt)
			->setMethodPrompt($secondLevelPrompt)
		;

		$this->addField($outputField);

		$testRunField = new test\run\cli();
		$testRunField->setPrompt($firstLevelPrompt);

		$this->addField($testRunField);

		$testDurationField = new test\duration\cli();
		$testDurationField->setPrompt($secondLevelPrompt);

		$this->addField($testDurationField);

		$testMemoryField = new test\memory\cli();
		$testMemoryField->setPrompt($secondLevelPrompt);

		$this->addField($testMemoryField);
	}
}
