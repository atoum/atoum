<?php

namespace mageekguy\atoum\tests\units\reports\realtime;

use
	mageekguy\atoum,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields,
	mageekguy\atoum\reports\realtime\cli as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\reports\realtime');
	}

	public function test__construct()
	{
		$this
			->define($atoumPathField = new fields\runner\atoum\path\cli())
				->and($atoumPathField
					->setPrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('1;36'))
				)
			->define($atoumVersionField = new fields\runner\atoum\version\cli())
				->and($atoumVersionField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('1;36'))
				)
			->define($phpPathField = new fields\runner\php\path\cli())
				->and($phpPathField
					->setPrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('1;36'))
				)
			->define($phpVersionField = new fields\runner\php\version\cli())
				->and($phpVersionField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('1;36'))
					->setVersionPrompt(new prompt('=> ', new colorizer('1;36')))
				)
			->define($runnerTestsDurationField = new fields\runner\tests\duration\cli())
				->and($runnerTestsDurationField
					->setPrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('1;36'))
				)
			->define($runnerTestsMemoryField = new fields\runner\tests\memory\cli())
				->and($runnerTestsMemoryField
					->setPrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('1;36'))
				)
			->define($runnerTestsCoverageField = new fields\runner\tests\coverage\cli())
				->and($runnerTestsCoverageField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('1;36'))
					->setClassPrompt(new prompt('=> ', new colorizer('1;36')))
					->setMethodPrompt(new prompt('==> ', new colorizer('1;36')))
				)
			->define($runnerDurationField = new fields\runner\duration\cli())
				->and($runnerDurationField
					->setPrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('1;36'))
				)
			->define($runnerResultField = new fields\runner\result\cli())
				->and($runnerResultField
					->setSuccessColorizer(new colorizer('0;37', '42'))
					->setFailureColorizer(new colorizer('0;37', '41'))
				)
			->define($runnerFailuresField = new fields\runner\failures\cli())
				->and($runnerFailuresField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;31'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;31')))
				)
			->define($runnerOutputsField = new fields\runner\outputs\cli())
				->and($runnerOutputsField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('1;36'))
					->setMethodPrompt(new prompt('=> ', new colorizer('1;36')))
				)
			->define($runnerErrorsField = new fields\runner\errors\cli())
				->and($runnerErrorsField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;33'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;33')))
					->setErrorPrompt(new prompt('==> ', new colorizer('0;33')))
				)
			->define($runnerExceptionsField = new fields\runner\exceptions\cli())
				->and($runnerExceptionsField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;35'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;35')))
					->setExceptionPrompt(new prompt('==> ', new colorizer('0;35')))
				)
			->define($runnerUncompletedField = new fields\runner\tests\uncompleted\cli())
				->and($runnerUncompletedField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;37'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;37')))
					->setOutputPrompt(new prompt('==> ', new colorizer('0;37')))
				)
			->define($runnerVoidField = new fields\runner\tests\void\cli())
				->and($runnerVoidField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;34'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;34')))
				)
			->define($runnerSkippedField = new fields\runner\tests\skipped\cli())
				->and($runnerSkippedField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;90'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;90')))
				)
			->define($testRunField = new fields\test\run\cli())
				->and($testRunField
					->setPrompt(new prompt('> '))
					->setColorizer(new colorizer('1;36'))
				)
			->define($testDurationField = new fields\test\duration\cli())
				->and($testDurationField
					->setPrompt(new prompt('=> ', new colorizer('1;36')))
				)
			->define($testMemoryField = new fields\test\memory\cli())
				->and($testMemoryField
					->setPrompt(new prompt('=> ', new colorizer('1;36')))
				)
			->if($report = new testedClass())
			->then
				->object($report->getLocale())->isEqualTo(new atoum\locale())
				->object($report->getAdapter())->isEqualTo(new atoum\adapter())
				->array($report->getFields())->isEqualTo(array(
						$atoumPathField,
						$atoumVersionField,
						$phpPathField,
						$phpVersionField,
						$runnerTestsDurationField,
						$runnerTestsMemoryField,
						$runnerTestsCoverageField,
						$runnerDurationField,
						$runnerResultField,
						$runnerFailuresField,
						$runnerOutputsField,
						$runnerErrorsField,
						$runnerExceptionsField,
						$runnerUncompletedField,
						$runnerVoidField,
						$runnerSkippedField,
						$testRunField,
						new fields\test\event\cli(),
						$testDurationField,
						$testMemoryField
					)
				)
		;
	}
}
