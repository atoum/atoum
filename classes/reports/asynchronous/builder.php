<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\report\fields\test,
	mageekguy\atoum\report\fields\runner
;

class builder extends atoum\reports\asynchronous
{
	public function __construct()
	{
		parent::__construct();

		$secondLevelPrompt = new prompt('   ');
		$thirdLevelPrompt = new prompt('      ');

		$this->addField(new runner\php\path\cli());

		$phpVersionField = new runner\php\version\cli();
		$phpVersionField->setVersionPrompt($secondLevelPrompt);

		$this->addField($phpVersionField);

		$this
			->addField(new runner\duration\cli())
			->addField(new runner\result\cli())
		;

		$failuresField = new runner\failures\cli();
		$failuresField->setMethodPrompt($secondLevelPrompt);

		$this->addField($failuresField);

		$outputsField = new runner\outputs\cli();
		$outputsField->setMethodPrompt($secondLevelPrompt);

		$this->addField($outputsField);

		$errorsField = new runner\errors\cli();
		$errorsField
			->setMethodPrompt($secondLevelPrompt)
			->setErrorPrompt($thirdLevelPrompt)
		;

		$this->addField($errorsField);

		$exceptionsField = new runner\exceptions\cli();
		$exceptionsField
			->setMethodPrompt($secondLevelPrompt)
			->setExceptionPrompt($thirdLevelPrompt)
		;

		$this->addField($exceptionsField);

		$uncompletedField = new runner\tests\uncompleted\cli();
		$uncompletedField
			->setMethodPrompt($secondLevelPrompt)
			->setOutputPrompt($thirdLevelPrompt)
		;

		$this->addField($uncompletedField);

		$this
			->addField(new runner\tests\duration\cli())
			->addField(new runner\tests\memory\cli())
		;

		$coverageField = new runner\tests\coverage\cli();
		$coverageField
			->setClassPrompt($secondLevelPrompt)
			->setMethodPrompt($thirdLevelPrompt)
		;

		$this
			->addField($coverageField)
			->addField(new test\run\cli())
		;

		$durationField = new test\duration\cli();
		$durationField->setPrompt($secondLevelPrompt);

		$this->addField($durationField);

		$memoryField = new test\memory\cli();
		$memoryField->setPrompt($secondLevelPrompt);

		$this->addField($memoryField);
	}
}
