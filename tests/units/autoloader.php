<?php

namespace mageekguy\atoum\tests\units;

use \mageekguy\atoum;

require_once(__DIR__ . '/../../runners/autorunner.php');

class autoloader extends atoum\test
{
	public function testGetPath()
	{
		$this->assert->variable(atoum\autoloader::getPath(uniqid()))->isNull();
		$this->assert->variable(atoum\autoloader::getPath('\mageekguy\atoum'))->isNull();

		$class = uniqid();

		$this->assert->string(atoum\autoloader::getPath('\mageekguy\atoum\\' . $class))->isEqualTo(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . $class . '.php');
	}
}

?>
