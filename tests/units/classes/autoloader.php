<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class autoloader extends atoum\test
{
	public function testGetDirectories()
	{
		$autoloader = new atoum\autoloader();

		$this->assert
            ->array($directories = $autoloader->getDirectories())->hasKey('mageekguy\atoum')
            ->array($directories['mageekguy\atoum'])->isEqualTo(array(atoum\directory . DIRECTORY_SEPARATOR . 'classes'));
	}

	public function testGetPath()
	{
		$autoloader = new atoum\autoloader();

		$this->assert
			->variable($autoloader->getPath(uniqid()))->isNull()
			->variable($autoloader->getPath('mageekguy\atoum'))->isNull()
			->variable($autoloader->getPath('\mageekguy\atoum'))->isNull()
			->string($autoloader->getPath('mageekguy\atoum\test'))->isEqualTo(atoum\directory . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'test.php')
			->variable($autoloader->getPath('\mageekguy\atoum\test'))->isNull()
		;
	}
}

?>
