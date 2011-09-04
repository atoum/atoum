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
		$autoloader = new atoum\autoloader();

		$this->assert
			->array($autoloader->getDirectories())->atKey('mageekguy\atoum')->contains(array(atoum\directory . '/classes'))
		;
	}

	public function testGetPath()
	{
		$autoloader = new atoum\autoloader();

		$this->assert
			->variable($autoloader->getPath(uniqid()))->isNull()
			->variable($autoloader->getPath('mageekguy\atoum'))->isNull()
			->variable($autoloader->getPath('\mageekguy\atoum'))->isNull()
			->string($autoloader->getPath('mageekguy\atoum\test'))->isEqualTo(atoum\directory . '/classes/test.php')
			->variable($autoloader->getPath('\mageekguy\atoum\test'))->isNull()
		;
	}
}

?>
