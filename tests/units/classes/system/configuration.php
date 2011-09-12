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
							'version' => $configuration->getOsVersion(),
							'arch' => $configuration->getOsArchitecture()
						),
					'PHP' => array(
							'version' => $configuration->getPhpVersion(),
							'extensions' => $configuration->getPhpExtensions()
						)
				)
			)
		;
	}

	public function testGetOsVersion()
	{
		$configuration = new system\configuration();

		$this->assert
			->string($configuration->getOsVersion())->isEqualTo(php_uname('s'))
		;
	}

	public function testGetOsArchitecture()
	{
		$configuration = new system\configuration();

		$this->assert
			->string($configuration->getOsArchitecture())->isEqualTo(php_uname('m'))
		;
	}

	public function testGetPhpVersion()
	{
		$configuration = new system\configuration();

		$this->assert
			->string($configuration->getPhpVersion())->isEqualTo(phpversion())
		;
	}

	public function testGetPhpExtensions()
	{
		$configuration = new system\configuration();

		$this->assert
			->array($configuration->getPhpExtensions())->isEqualTo(array_merge(get_loaded_extensions(false), get_loaded_extensions(true)))
		;
	}

	public function test__toString()
	{
		$configuration = new system\configuration();

		$this->assert
			->castToString($configuration)->isEqualTo('=> OS:' . PHP_EOL .
				'==> Version: ' . $configuration->getOsVersion() . PHP_EOL .
				'==> Architecture: ' .$configuration->getOsArchitecture() . PHP_EOL .
				'=> PHP:' . PHP_EOL .
				'==> Version: ' . $configuration->getPhpVersion() . PHP_EOL .
				'==> Extensions:' . PHP_EOL . '===> ' . join(PHP_EOL . '===> ', $configuration->getPhpExtensions()) . PHP_EOL
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

		$this->mockGenerator
			->generate($this->getTestedClassName())
		;

		$otherConfiguration = new \mock\mageekguy\atoum\system\configuration();
		$otherConfiguration->getSignature = uniqid();

		$this->assert
			->boolean($configuration->isEqualTo($otherConfiguration))->isFalse()
		;

	}
}

?>
