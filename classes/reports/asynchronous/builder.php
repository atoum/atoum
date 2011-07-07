<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	\mageekguy\atoum,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\report\fields\test,
	\mageekguy\atoum\report\fields\runner
;

class builder extends atoum\reports\asynchronous
{
	public function __construct(atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($locale, $adapter);

		$runnerTestsDurationField = new runner\tests\duration\cli(null, '');
		$runnerTestsMemoryField = new runner\tests\memory\cli();
		$runnerTestsCoverageField = new runner\tests\coverage\cli(null, '', '   ', '      ');

		$testRunField = new test\run\cli(null, '');
		$testDurationField = new test\duration\cli(null, '   ');
		$testMemoryField = new test\memory\cli(null, '   ');

		$this
			->addRunnerField(
				new runner\atoum\cli(
				),
				array(atoum\runner::runStart)
			)
			->addRunnerField(
				new runner\php\path\cli(
					null,
					null,
					new prompt('   ')
				),
				array(atoum\runner::runStart)
			)
			->addRunnerField(
				new runner\php\version\cli(
					null,
					null,
					new prompt('   ')
				),
				array(atoum\runner::runStart)
			)
			->addRunnerField(
					new runner\duration\cli(
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(
				new runner\result\cli(
				),
				array(atoum\runner::runStop)
			)
			->addRunnerField(
				new runner\failures\cli(
					null,
					null,
					new prompt('   ')
				),
				array(atoum\runner::runStop)
			)
			->addRunnerField(
				new runner\outputs\cli(
					null,
					null,
					new prompt('   ')
				),
				array(atoum\runner::runStop)
			)
			->addRunnerField(
				new runner\errors\cli(
					null,
					null,
					new prompt('   '),
					null,
					new prompt('      ')
				),
				array(atoum\runner::runStop)
			)
			->addRunnerField(
				new runner\exceptions\cli(
					null,
					null,
					new prompt('   '),
					null,
					new prompt('      ')
				),
				array(atoum\runner::runStop)
			)
			->addRunnerField($runnerTestsDurationField, array(atoum\runner::runStop))
			->addRunnerField($runnerTestsMemoryField, array(atoum\runner::runStop))
			->addRunnerField($runnerTestsCoverageField, array(atoum\runner::runStop))
			->addTestField($testRunField, array(atoum\test::runStart))
			->addTestField($testDurationField, array(atoum\test::runStop))
			->addTestField($testMemoryField, array(atoum\test::runStop))
		;
	}
}

?>
