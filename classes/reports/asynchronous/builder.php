<?php

namespace atoum\reports\asynchronous;

use
	atoum,
	atoum\cli\prompt,
	atoum\cli\colorizer,
	atoum\exceptions,
	atoum\report\fields\test,
	atoum\report\fields\runner
;

class builder extends atoum\reports\asynchronous
{
	public function __construct(atoum\factory $factory = null)
	{
		parent::__construct($factory);

		$this
			->addField(new runner\atoum\cli())
			->addField(new runner\php\path\cli())
			->addField(
				new runner\php\version\cli(
					null,
					null,
					new prompt('   ')
				)
			)
			->addField(new runner\duration\cli())
			->addField(new runner\result\cli())
			->addField(
				new runner\failures\cli(
					null,
					null,
					new prompt('   ')
				)
			)
			->addField(
				new runner\outputs\cli(
					null,
					null,
					new prompt('   ')
				)
			)
			->addField(
				new runner\errors\cli(
					null,
					null,
					new prompt('   '),
					null,
					new prompt('      ')
				)
			)
			->addField(
				new runner\exceptions\cli(
					null,
					null,
					new prompt('   '),
					null,
					new prompt('      ')
				)
			)
			->addField(new runner\tests\uncompleted\cli(
					null,
					null,
					new prompt('   '),
					null,
					new prompt('      ')
				)
			)
			->addField(new runner\tests\duration\cli())
			->addField(new runner\tests\memory\cli())
			->addField(
				new runner\tests\coverage\cli(
					null,
					new prompt('   '),
					new prompt('      ')
				)
			)
			->addField(new test\run\cli())
			->addField(new test\duration\cli(
					new prompt('   ')
				)
			)
			->addField(new test\memory\cli(
					new prompt('   ')
				)
			)
		;
	}
}
