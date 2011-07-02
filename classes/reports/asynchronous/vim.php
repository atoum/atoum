<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	\mageekguy\atoum,
	\mageekguy\atoum\reports,
	\mageekguy\atoum\report\fields
;

class vim extends reports\asynchronous
{
	public function __construct(atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($locale, $adapter);

		$this
			->addRunnerField(new fields\runner\version\cli(null, new atoum\cli\colorizer()), array(atoum\runner::runStart))
			->addRunnerField(new fields\runner\tests\duration\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\tests\memory\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\tests\coverage\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\duration\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\result\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\failures\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\outputs\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\errors\cli(), array(atoum\runner::runStop))
			->addRunnerField(new fields\runner\exceptions\cli(), array(atoum\runner::runStop))
			->addTestField(new fields\test\run\cli(), array(atoum\test::runStart))
			->addTestField(new fields\test\duration\cli(), array(atoum\test::runStop))
			->addTestField(new fields\test\memory\cli(), array(atoum\test::runStop))
		;
	}

	public function __toString()
	{
		return parent::__toString() . '/* vim: set ft=atoum: */';
	}
}

?>
