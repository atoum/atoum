<?php

namespace atoum\tests\units\reports\asynchronous;

use
	atoum,
	atoum\cli\prompt,
	atoum\cli\colorizer,
	atoum\report\fields\test,
	atoum\report\fields\runner,
	atoum\reports\asynchronous as reports
;

require_once __DIR__ . '/../../../runner.php';

class builder extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubClassOf('atoum\reports\asynchronous');
	}

	public function test__construct()
	{
		$this
			->if($report = new reports\builder())
			->then
				->object($report->getFactory())->isInstanceOf('atoum\factory')
				->object($report->getLocale())->isInstanceOf('atoum\locale')
				->object($report->getAdapter())->isInstanceOf('atoum\adapter')
				->array($report->getFields())->isEqualTo(array(
						new runner\atoum\cli(),
						new runner\php\path\cli(),
						new runner\php\version\cli(
							null,
							null,
							new prompt('   ')
						),
						new runner\duration\cli(),
						new runner\result\cli(),
						new runner\failures\cli(
							null,
							null,
							new prompt('   ')
						),
						new runner\outputs\cli(
							null,
							null,
							new prompt('   ')
						),
						new runner\errors\cli(
							null,
							null,
							new prompt('   '),
							null,
							new prompt('      ')
						),
						new runner\exceptions\cli(
							null,
							null,
							new prompt('   '),
							null,
							new prompt('      ')
						),
						new runner\tests\uncompleted\cli(
							null,
							null,
							new prompt('   '),
							null,
							new prompt('      ')
						),
						new runner\tests\duration\cli(),
						new runner\tests\memory\cli(),
						new runner\tests\coverage\cli(null, new prompt('   '), new prompt('      ')),
						new test\run\cli(),
						new test\duration\cli(new prompt('   ')),
						new test\memory\cli(new prompt('   '))
					)
				)
			->if($factory = new atoum\factory())
			->and($factory['atoum\locale'] = $locale = new atoum\locale())
			->and($factory['atoum\adapter'] = $adapter = new atoum\adapter())
			->and($report = new reports\builder($factory))
			->then
				->object($report->getFactory())->isIdenticalTo($factory)
				->object($report->getLocale())->isIdenticalTo($locale)
				->object($report->getAdapter())->isIdenticalTo($adapter)
				->array($report->getFields())->isEqualTo(array(
						new runner\atoum\cli(),
						new runner\php\path\cli(),
						new runner\php\version\cli(
							null,
							null,
							new prompt('   ')
						),
						new runner\duration\cli(),
						new runner\result\cli(),
						new runner\failures\cli(
							null,
							null,
							new prompt('   ')
						),
						new runner\outputs\cli(
							null,
							null,
							new prompt('   ')
						),
						new runner\errors\cli(
							null,
							null,
							new prompt('   '),
							null,
							new prompt('      ')
						),
						new runner\exceptions\cli(
							null,
							null,
							new prompt('   '),
							null,
							new prompt('      ')
						),
						new runner\tests\uncompleted\cli(
							null,
							null,
							new prompt('   '),
							null,
							new prompt('      ')
						),
						new runner\tests\duration\cli(),
						new runner\tests\memory\cli(),
						new runner\tests\coverage\cli(null, new prompt('   '), new prompt('      ')),
						new test\run\cli(),
						new test\duration\cli(new prompt('   ')),
						new test\memory\cli(new prompt('   '))
					)
				)
		;
	}
}
