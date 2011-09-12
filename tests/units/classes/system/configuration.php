<?php

namespace mageekguy\atoum\tests\units\system;

use
	mageekguy\atoum,
	mageekguy\atoum\system
;

require_once __DIR__ . '/../../runner.php';

class configuration extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass()->hasInterface('Serializable')
		;
	}

	public function test__construct()
	{
		$configuration = new system\configuration();

		$this->assert
			->array($configuration->get())->isEqualTo(array(
					'OS' => array(
							'version' => php_uname('s'),
							'arch' => php_uname('m')
						),
					'PHP' => array(
							'version' => phpversion(),
							'extensions' => array_merge(get_loaded_extensions(false), get_loaded_extensions(true))
						)
				)
			)
		;
	}

	public function testSerializable()
	{
		$configuration = new system\configuration();

		$this->assert
			->object(unserialize(serialize($configuration)))->isEqualTo($configuration)
		;
	}

	public function testGetSignature()
	{
		$configuration = new system\configuration();

		$this->assert
			->string($configuration->getSignature())->isEqualTo(sha1(serialize($configuration)))
		;
	}

	public function testIsEqualTo()
	{
		$configuration = new system\configuration();

		$this->assert
			->boolean($configuration->isEqualTo($configuration))->isTrue()
		;
	}
}

?>
