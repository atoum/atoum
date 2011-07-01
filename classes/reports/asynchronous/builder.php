<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	\mageekguy\atoum,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\report\fields
;

class builder extends atoum\reports\asynchronous
{
	public function __construct(atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($locale, $adapter);

		$runnerVersionField = new fields\runner\version\cli(new prompt(''));
		$runnerPhpField = new fields\runner\php\cli(new prompt(''), null, new prompt('   '));
		$runnerDurationField = new fields\runner\duration\cli(null, '');
		$runnerResultField = new fields\runner\result\cli();
		$runnerFailuresField = new fields\runner\failures\cli(null, '', '   ');
		$runnerOutputsField = new fields\runner\outputs\cli(null, '', '   ');
		$runnerErrorsField = new fields\runner\errors\cli(null, '', '   ', '      ');
		$runnerExceptionsField = new fields\runner\exceptions\cli(null, '', '   ', '      ');
		$runnerTestsDurationField = new fields\runner\tests\duration\cli(null, '');
		$runnerTestsMemoryField = new fields\runner\tests\memory\cli(null, '');
		$runnerTestsCoverageField = new fields\runner\tests\coverage\cli(null, '', '   ', '      ');

		$testRunField = new fields\test\run\cli(null, '');
		$testDurationField = new fields\test\duration\cli(null, '   ');
		$testMemoryField = new fields\test\memory\cli(null, '   ');

		$this
			->addRunnerField($runnerVersionField, array(atoum\runner::runStart))
			->addRunnerField($runnerPhpField, array(atoum\runner::runStart))
			->addRunnerField($runnerTestsDurationField, array(atoum\runner::runStop))
			->addRunnerField($runnerTestsMemoryField, array(atoum\runner::runStop))
			->addRunnerField($runnerTestsCoverageField, array(atoum\runner::runStop))
			->addRunnerField($runnerDurationField, array(atoum\runner::runStop))
			->addRunnerField($runnerResultField, array(atoum\runner::runStop))
			->addRunnerField($runnerFailuresField, array(atoum\runner::runStop))
			->addRunnerField($runnerOutputsField, array(atoum\runner::runStop))
			->addRunnerField($runnerErrorsField, array(atoum\runner::runStop))
			->addRunnerField($runnerExceptionsField, array(atoum\runner::runStop))
			->addTestField($testRunField, array(atoum\test::runStart))
			->addTestField($testDurationField, array(atoum\test::runStop))
			->addTestField($testMemoryField, array(atoum\test::runStop))
		;
	}
}

?>
