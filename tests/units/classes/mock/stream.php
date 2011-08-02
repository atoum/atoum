<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum
;

require_once(__DIR__ . '/../../runner.php');

class stream extends atoum\test
{
	public function testClassConstants()
	{
		$this->assert
			->string(atoum\mock\stream::name)->isEqualTo('atoum')
		;
	}

	public function testRegister()
	{
		atoum\mock\stream::register();

		$this->assert
			->array(stream_get_wrappers())->contain(atoum\mock\stream::name)
		;
	}

	public function testGet()
	{
		$this->assert
			->object(atoum\mock\stream::get(uniqid()))->isEqualTo(new atoum\mock\stream\controller())
		;
	}
}

?>
