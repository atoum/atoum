<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\report\fields
;

class builder extends atoum\reports\asynchronous
{
	public function __construct(atoum\locale $locale = null)
	{
		parent::__construct($locale);

		$runnerVersionField = new fields\runner\version\string(null, '');
		$runnerPhpField = new fields\runner\php\string(null, '', '   ');
		$runnerDurationField = new fields\runner\duration\string(null, '');
		$runnerResultField = new fields\runner\result\string(null, '');
		$runnerFailuresField = new fields\runner\failures\string(null, '', '   ');
		$runnerOutputsField = new fields\runner\outputs\string(null, '', '   ');
		$runnerErrorsField = new fields\runner\errors\string(null, '', '   ', '      ');
		$runnerExceptionsField = new fields\runner\exceptions\string(null, '', '   ', '      ');
		$runnerTestsDurationField = new fields\runner\tests\duration\string(null, '');
		$runnerTestsMemoryField = new fields\runner\tests\memory\string(null, '');
		$runnerTestsCoverageField = new fields\runner\tests\coverage\string(null, '', '   ', '      ');

		$testRunField = new fields\test\run\string(null, '');
		$testDurationField = new fields\test\duration\string(null, '   ');
		$testMemoryField = new fields\test\memory\string(null, '   ');

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
