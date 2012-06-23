<?php

namespace atoum\tests\units;

use
	atoum
;

require_once __DIR__ . '/../runner.php';

class autoloader extends atoum\test
{
	public function testGetDirectories()
	{
		$autoloader = new atoum\autoloader();

		$this->assert
            ->array($directories = $autoloader->getDirectories())->hasKey('atoum')
            ->array($directories['atoum'])->isEqualTo(array(atoum\directory . (\phar::running() ? '/' : DIRECTORY_SEPARATOR) . 'classes'));
	}
}
