<?php

namespace mageekguy\atoum\tests\units\tools\arguments;

use
	mageekguy\atoum,
	mageekguy\atoum\tools
;

require_once(__DIR__ . '/../../../runner.php');

class decorator extends atoum\test
{
	public function testDecorate()
	{
		$decorator = new tools\arguments\decorator();

		$stream = atoum\mock\stream::get('resource');
		$stream->fopen = true;

		$this->assert
			->string($decorator->decorate(array()))->isEmpty()
			->string($decorator->decorate(array(1)))->isEqualTo('integer(1)')
			->string($decorator->decorate(array(1, 2)))->isEqualTo('integer(1), integer(2)')
			->string($decorator->decorate(array(1.0)))->isEqualTo('float(1)')
			->string($decorator->decorate(array(1.0, 2.1)))->isEqualTo('float(1), float(2.1)')
			->string($decorator->decorate(array(true)))->isEqualTo('TRUE')
			->string($decorator->decorate(array(false)))->isEqualTo('FALSE')
			->string($decorator->decorate(array(false, true)))->isEqualTo('FALSE, TRUE')
			->string($decorator->decorate(array(null)))->isEqualTo('NULL')
			->string($decorator->decorate(array($this)))->isEqualTo('object(' . __CLASS__ . ')')
		;

		$resource = fopen('atoum://resource', 'r');
		ob_start();
		var_dump($resource);
		$dump = ob_get_clean();

		$this->assert
			->string($decorator->decorate(array($resource)))->isEqualTo($dump)
		;
	}
}

?>
