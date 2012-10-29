<?php

namespace mageekguy\atoum\tests\units\reports\realtime\cli;

use
	mageekguy\atoum,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields,
	mageekguy\atoum\reports\realtime\cli\light as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class light extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\reports\realtime');
	}

	public function test__construct()
	{
		$this
			->define($eventField = new fields\runner\event\cli())
			->define($resultField = new fields\runner\result\cli())
			->define($resultField
				->setSuccessColorizer(new colorizer('0;37', '42'))
				->setFailureColorizer(new colorizer('0;37', '41'))
			)
			->define($failuresField = new fields\runner\failures\cli())
			->define($failuresField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('1;36'))
					->setMethodPrompt(new prompt('=> ', new colorizer('1;36')))
			)
			->define($outputsField = new fields\runner\outputs\cli())
			->define($outputsField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('1;36'))
					->setMethodPrompt(new prompt('=> ', new colorizer('1;36')))
			)
			->define($errorsField = new fields\runner\errors\cli())
			->define($errorsField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;33'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;33')))
					->setErrorPrompt(new prompt('==> ', new colorizer('0;33')))
			)
			->define($exceptionsField = new fields\runner\exceptions\cli())
			->define($exceptionsField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;35'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;35')))
					->setExceptionPrompt(new prompt('==> ', new colorizer('0;35')))
			)
			->define($uncompletedTestField = new fields\runner\tests\uncompleted\cli())
			->define($uncompletedTestField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;37'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;37')))
					->setOutputPrompt(new prompt('==> ', new colorizer('0;37')))
			)
			->define($voidTestField = new fields\runner\tests\void\cli())
			->define($voidTestField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;34'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;34')))
			)
			->define($skippedTestField = new fields\runner\tests\skipped\cli())
				->and($skippedTestField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;90'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;90')))
				)
			->if($report = new testedClass())
			->then
				->object($report->getLocale())->isEqualTo(new atoum\locale())
				->object($report->getAdapter())->isEqualTo(new atoum\adapter())
				->array($report->getFields())->isEqualTo(array(
						$eventField,
						$resultField,
						$failuresField,
						$outputsField,
						$errorsField,
						$exceptionsField,
						$uncompletedTestField,
						$voidTestField,
						$skippedTestField
					)
				)
		;
	}
}
