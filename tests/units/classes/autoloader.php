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
            ->array($directories['mageekguy\atoum'])->isEqualTo(array(atoum\directory . (\phar::running() ? '/' : DIRECTORY_SEPARATOR) . 'classes'));
	}
}
