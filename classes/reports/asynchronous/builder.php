<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\report\fields\test,
	mageekguy\atoum\report\fields\runner
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
