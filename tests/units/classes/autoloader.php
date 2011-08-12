<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once(__DIR__ . '/../runner.php');
require_once(__DIR__ . '/../../../constants.php');

class autoloader extends atoum\test
{
	public function testGetDirectories()
	{
		$this->assert
			->array(atoum\autoloader::getDirectories())->atKey('mageekguy\atoum')->contains(array(atoum\directory . '/classes'))
		;
	}

	public function testGetPath()
	{
		$this->assert
			->variable(atoum\autoloader::getPath(uniqid()))->isNull()
			->variable(atoum\autoloader::getPath('mageekguy\atoum'))->isNull()
			->variable(atoum\autoloader::getPath('\mageekguy\atoum'))->isNull()
			->string(atoum\autoloader::getPath('mageekguy\atoum\test'))->isEqualTo(atoum\directory . '/classes/test.php')
			->variable(atoum\autoloader::getPath('\mageekguy\atoum\test'))->isNull()
		;
	}
}

?>
