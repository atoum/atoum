<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	\mageekguy\atoum,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\reports,
	\mageekguy\atoum\report\fields\test,
	\mageekguy\atoum\report\fields\runner
;

class vim extends reports\asynchronous
{
	const defaultOutputTitlePrompt = '> ';
	const defaultOutputMethodPrompt = '=> ';

	public function __construct(atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($locale, $adapter);

		$this
			->addRunnerField(new runner\version\cli(null, new colorizer()), array(atoum\runner::runStart))
			->addRunnerField(new runner\tests\duration\cli(), array(atoum\runner::runStop))
			->addRunnerField(new runner\tests\memory\cli(), array(atoum\runner::runStop))
			->addRunnerField(new runner\tests\coverage\cli(), array(atoum\runner::runStop))
			->addRunnerField(new runner\duration\cli(), array(atoum\runner::runStop))
			->addRunnerField(new runner\result\cli(), array(atoum\runner::runStop))
			->addRunnerField(new runner\failures\cli(
						null,
						new colorizer(),
						new prompt(runner\failures\cli::defaultMethodPrompt, new colorizer()),
						new colorizer()
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(
				new runner\outputs\cli(
					new prompt(self::defaultOutputTitlePrompt),
					null,
					new prompt(self::defaultOutputMethodPrompt)
				),
				array(atoum\runner::runStop)
			)
			->addRunnerField(new runner\errors\cli(
						null,
						new colorizer(),
						new prompt(runner\errors\cli::defaultMethodPrompt, new colorizer()),
						new colorizer(),
						new prompt(runner\errors\cli::defaultErrorPrompt, new colorizer()),
						new colorizer()
					),
					array(atoum\runner::runStop)
				)
			->addRunnerField(new runner\exceptions\cli(
						null,
						new colorizer(),
						new prompt(runner\exceptions\cli::defaultMethodPrompt, new colorizer()),
						new colorizer(),
						new prompt(runner\exceptions\cli::defaultExceptionPrompt, new colorizer()),
						new colorizer()
					),
					array(atoum\runner::runStop)
				)
			->addTestField(new test\run\cli(), array(atoum\test::runStart))
			->addTestField(new test\duration\cli(), array(atoum\test::runStop))
			->addTestField(new test\memory\cli(), array(atoum\test::runStop))
		;
	}

	public function __toString()
	{
		return parent::__toString() . '/* vim: set ft=atoum: */';
	}
}

?>
