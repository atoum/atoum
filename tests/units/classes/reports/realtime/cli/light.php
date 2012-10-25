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
			->if($eventField = new fields\runner\event\cli())
			->and($resultField = new fields\runner\result\cli())
			->and($resultField
				->setSuccessColorizer(new colorizer('0;37', '42'))
				->setFailureColorizer(new colorizer('0;37', '41'))
			)
			->and($failuresField = new fields\runner\failures\cli())
			->and($failuresField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('1;36'))
					->setMethodPrompt(new prompt('=> ', new colorizer('1;36')))
			)
			->and($outputsField = new fields\runner\outputs\cli())
			->and($outputsField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('1;36'))
					->setMethodPrompt(new prompt('=> ', new colorizer('1;36')))
			)
			->and($errorsField = new fields\runner\errors\cli())
			->and($errorsField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;33'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;33')))
					->setErrorPrompt(new prompt('==> ', new colorizer('0;33')))
			)
			->and($exceptionsField = new fields\runner\exceptions\cli())
			->and($exceptionsField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;35'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;35')))
					->setExceptionPrompt(new prompt('==> ', new colorizer('0;35')))
			)
			->and($uncompletedTestField = new fields\runner\tests\uncompleted\cli())
			->and($uncompletedTestField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;37'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;37')))
					->setOutputPrompt(new prompt('==> ', new colorizer('0;37')))
			)
			->and($voidTestField = new fields\runner\tests\void\cli())
			->and($voidTestField
					->setTitlePrompt(new prompt('> '))
					->setTitleColorizer(new colorizer('0;34'))
					->setMethodPrompt(new prompt('=> ', new colorizer('0;34')))
					->setOutputPrompt(new prompt('==> ', new colorizer('0;34')))
			)
			->and($report = new testedClass())
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
						$voidTestField
					)
				)
		;
	}
}
