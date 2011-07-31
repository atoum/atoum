<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\mock
;

require_once(__DIR__ . '/../../runner.php');

class stream extends atoum\test
{
	public function testCreate()
	{
		$streamController = mock\stream::create($name = 'atoum');

		$this->assert
			->object($streamController)->isInstanceOf('mageekguy\atoum\mock\stream\controller')
			->array(stream_get_wrappers())->contain($name)
		;
	}

	public function testGet()
	{
		$this->assert
			->object(mock\stream::get(uniqid()))->isEqualTo(new mock\stream\controller())
		;
	}
}

?>
