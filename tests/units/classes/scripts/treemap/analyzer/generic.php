<?php

namespace mageekguy\atoum\tests\units\scripts\treemap\analyzer;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts\treemap\analyzer\generic as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class generic extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($generic = new testedClass($metricName = 'metric'))
			->then
				->string($generic->getMetricName())->isEqualTo($metricName)
				->string($generic->getMetricLabel())->isEqualTo(ucfirst($metricName))
				->object($callback = $generic->getCallback())->isInstanceOf('closure')
				->integer($callback())->isZero()
			->if($generic = new testedClass($metricName = uniqid(), $metricLabel = uniqid()))
			->then
				->string($generic->getMetricName())->isEqualTo($metricName)
				->string($generic->getMetricLabel())->isEqualTo(ucfirst($metricLabel))
				->object($callback = $generic->getCallback())->isInstanceOf('closure')
				->integer($callback())->isZero()
			->if($generic = new testedClass($metricName = uniqid(), $metricLabel = uniqid(), $callback = function() {}))
			->then
				->string($generic->getMetricName())->isEqualTo($metricName)
				->string($generic->getMetricLabel())->isEqualTo(ucfirst($metricLabel))
				->object($callback = $generic->getCallback())->isIdenticalTo($callback)
				->variable($callback())->isNull()
		;
	}

	public function testSetCallback()
	{
		$this
			->if($generic = new testedClass(uniqid()))
			->then
				->object($generic->setCallback($callback = function() {}))->isIdenticalTo($generic)
				->object($generic->getCallback())->isIdenticalTo($callback)
		;
	}

	public function testSetMetricName()
	{
		$this
			->if($generic = new testedClass(uniqid()))
			->then
				->object($generic->setMetricName($metricName = 'metric'))->isIdenticalTo($generic)
				->string($generic->getMetricName())->isEqualTo($metricName)
				->string($generic->getMetricLabel())->isEqualTo(ucfirst($metricName))
		;
	}

	public function testSetMetricLabel()
	{
		$this
			->if($generic = new testedClass(uniqid()))
			->then
				->object($generic->setMetricLabel($metricLabel = 'metric'))->isIdenticalTo($generic)
				->string($generic->getMetricLabel())->isEqualTo($metricLabel)
				->string($generic->getMetricLabel())->isEqualTo($metricLabel)
		;
	}

	public function testGetMetricFromFile()
	{
		$this
			->if($generic = new testedClass(uniqid()))
			->then
				->integer($generic->getMetricFromFile(new \splFileInfo(__FILE__)))->isZero()
			->if($generic->setCallback(function() {}))
			->then
				->variable($generic->getMetricFromFile(new \splFileInfo(__FILE__)))->isNull()
		;
	}
}
