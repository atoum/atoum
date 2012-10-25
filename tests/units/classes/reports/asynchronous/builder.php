<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\test,
	mageekguy\atoum\report\fields\runner,
	mageekguy\atoum\reports\asynchronous\builder as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class builder extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\reports\asynchronous');
	}

	public function test__construct()
	{
		$this
			->define($phpVersionField = new runner\php\version\cli())
				->and($phpVersionField->setVersionPrompt(new prompt('   ')))
			->define($failuresField = new runner\failures\cli())
				->and($failuresField->setMethodPrompt(new prompt('   ')))
			->define($outputsField = new runner\outputs\cli())
				->and($outputsField->setMethodPrompt(new prompt('   ')))
			->define($errorsField = new runner\errors\cli())
				->and($errorsField
					->setMethodPrompt(new prompt('   '))
					->setErrorPrompt(new prompt('      '))
				)
			->define($exceptionsField = new runner\exceptions\cli())
				->and($exceptionsField
					->setMethodPrompt(new prompt('   '))
					->setExceptionPrompt(new prompt('      '))
				)
			->define($uncompletedField = new runner\tests\uncompleted\cli())
				->and($uncompletedField
					->setMethodPrompt(new prompt('   '))
					->setOutputPrompt(new prompt('      '))
				)
			->define($coverageField = new runner\tests\coverage\cli())
				->and($coverageField
					->setClassPrompt(new prompt('   '))
					->setMethodPrompt(new prompt('      '))
				)
			->define($durationField = new test\duration\cli())
				->and($durationField->setPrompt(new prompt('   ')))
			->define($memoryField = new test\memory\cli())
				->and($memoryField->setPrompt(new prompt('   ')))
			->if($report = new testedClass())
			->then
				->object($report->getLocale())->isEqualTo(new atoum\locale())
				->object($report->getAdapter())->isEqualTo(new atoum\adapter())
				->array($report->getFields())->isEqualTo(array(
						new runner\php\path\cli(),
						$phpVersionField,
						new runner\duration\cli(),
						new runner\result\cli(),
						$failuresField,
						$outputsField,
						$errorsField,
						$exceptionsField,
						$uncompletedField,
						new runner\tests\duration\cli(),
						new runner\tests\memory\cli(),
						$coverageField,
						new test\run\cli(),
						$durationField,
						$memoryField
					)
				)
		;
	}
}
