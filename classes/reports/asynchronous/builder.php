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

		$this
			->addRunnerField(
				new runner\atoum\cli(
				),
				array(atoum\runner::runStart)
			)
			->addRunnerField(
				new runner\php\path\cli(
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
			->addRunnerField(
				new runner\tests\duration\cli(),
				array(atoum\runner::runStop)
			)
			->addRunnerField(
				new runner\tests\memory\cli(),
				array(atoum\runner::runStop)
			)
			->addRunnerField(
				new runner\tests\coverage\cli(null, new prompt('   '), new prompt('      ')),
				array(atoum\runner::runStop)
			)
			->addTestField(
				new test\run\cli(),
				array(atoum\test::runStart)
			)
			->addTestField(new test\duration\cli(new prompt('   ')), array(atoum\test::runStop))
			->addTestField(new test\memory\cli(new prompt('   ')), array(atoum\test::runStop))
		;
	}
}

?>
