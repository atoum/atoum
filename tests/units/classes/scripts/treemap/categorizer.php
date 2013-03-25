<?php

namespace mageekguy\atoum\tests\units\scripts\treemap;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts\treemap\categorizer as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class categorizer extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($categorizer = new testedClass($name = uniqid()))
			->then
				->string($categorizer->getName())->isEqualTo($name)
				->object($callback = $categorizer->getCallback())->isInstanceOf('closure')
				->boolean($callback())->isFalse()
				->string($categorizer->getMinDepthColor())->isEqualTo('#94ff5a')
				->string($categorizer->getMaxDepthColor())->isEqualTo('#00500f')
		;
	}

	public function testSetMinDepthColor()
	{
		$this
			->if($categorizer = new testedClass(uniqid()))
			->then
				->object($categorizer->setMinDepthColor($color = '#000000'))->isIdenticalTo($categorizer)
				->string($categorizer->getMinDepthColor())->isEqualTo($color)
				->object($categorizer->setMinDepthColor($color = '000000'))->isIdenticalTo($categorizer)
				->string($categorizer->getMinDepthColor())->isEqualTo('#' . $color)
				->object($categorizer->setMinDepthColor($color = '#ffffff'))->isIdenticalTo($categorizer)
				->string($categorizer->getMinDepthColor())->isEqualTo($color)
				->object($categorizer->setMinDepthColor($color = 'ffffff'))->isIdenticalTo($categorizer)
				->string($categorizer->getMinDepthColor())->isEqualTo('#' . $color)
				->object($categorizer->setMinDepthColor($color = '#FFFFFF'))->isIdenticalTo($categorizer)
				->string($categorizer->getMinDepthColor())->isEqualTo($color)
				->object($categorizer->setMinDepthColor($color = 'FFFFFF'))->isIdenticalTo($categorizer)
				->string($categorizer->getMinDepthColor())->isEqualTo('#' . $color)
				->exception(function() use ($categorizer, & $color) { $categorizer->setMinDepthColor('#00000g'); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Color must be in hexadecimal format')
				->exception(function() use ($categorizer, & $color) { $categorizer->setMinDepthColor('#00000'); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Color must be in hexadecimal format')
				->exception(function() use ($categorizer, & $color) { $categorizer->setMinDepthColor('@000000'); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Color must be in hexadecimal format')
		;
	}

	public function testSetMaxDepthColor()
	{
		$this
			->if($categorizer = new testedClass(uniqid()))
			->then
				->object($categorizer->setMaxDepthColor($color = '#000000'))->isIdenticalTo($categorizer)
				->string($categorizer->getMaxDepthColor())->isEqualTo($color)
				->object($categorizer->setMaxDepthColor($color = '000000'))->isIdenticalTo($categorizer)
				->string($categorizer->getMaxDepthColor())->isEqualTo('#' . $color)
				->object($categorizer->setMaxDepthColor($color = '#ffffff'))->isIdenticalTo($categorizer)
				->string($categorizer->getMaxDepthColor())->isEqualTo($color)
				->object($categorizer->setMaxDepthColor($color = 'ffffff'))->isIdenticalTo($categorizer)
				->string($categorizer->getMaxDepthColor())->isEqualTo('#' . $color)
				->object($categorizer->setMaxDepthColor($color = '#FFFFFF'))->isIdenticalTo($categorizer)
				->string($categorizer->getMaxDepthColor())->isEqualTo($color)
				->object($categorizer->setMaxDepthColor($color = 'FFFFFF'))->isIdenticalTo($categorizer)
				->string($categorizer->getMaxDepthColor())->isEqualTo('#' . $color)
				->exception(function() use ($categorizer, & $color) { $categorizer->setMaxDepthColor('#00000g'); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Color must be in hexadecimal format')
				->exception(function() use ($categorizer, & $color) { $categorizer->setMaxDepthColor('#00000'); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Color must be in hexadecimal format')
				->exception(function() use ($categorizer, & $color) { $categorizer->setMaxDepthColor('@000000'); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Color must be in hexadecimal format')
		;
	}

	public function testSetCallback()
	{
		$this
			->if($categorizer = new testedClass(uniqid()))
			->then
				->object($categorizer->setCallback($callback = function() {}))->isIdenticalTo($categorizer)
				->object($categorizer->getCallback())->isIdenticalTo($callback)
		;
	}

	public function testCategorize()
	{
		$this
			->if($categorizer = new testedClass(uniqid()))
			->then
				->boolean($categorizer->categorize(new \splFileInfo(__FILE__)))->isFalse()
			->if($categorizer->setCallback(function() { return true; }))
			->then
				->boolean($categorizer->categorize(new \splFileInfo(__FILE__)))->isTrue()
		;
	}
}
