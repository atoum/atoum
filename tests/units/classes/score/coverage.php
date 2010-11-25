<?php

namespace mageekguy\atoum\tests\units\score;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\score;

require_once(__DIR__ . '/../../runner.php');

class coverage extends atoum\test
{
	public function test__construct()
	{
		$coverage = new score\coverage();

		$this->assert
			->array($coverage->getLines())->isEmpty()
		;
	}

	public function testSetxdebugData()
	{
		$coverage = new score\coverage();

		$this->assert
			->object($coverage->addxdebugData($this, array()))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEmpty()
		;

		$mockController = new mock\controller();
		$mockController->__construct = function() {};
		$mockController->getName = function() {};
		$mockController->getFileName = function() use (& $classFile) { return $classFile; };

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\reflectionClass');

		$coverage->setReflectionClassInjector(function($class) use ($mockController) { return new mock\reflectionClass($class, $mockController); });

		$classFile = uniqid();

		$xdebugData = array(
		  $classFile =>
			 array(
				5 => 1,
				6 => 2,
				7 => 3,
				8 => 2,
				9 => 1
			),
		  uniqid() =>
			 array(
				5 => 2,
				6 => 3,
				7 => 4,
				8 => 3,
				9 => 2
			)
		);

		$this->assert
			->object($coverage->addxdebugData($this, $xdebugData))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEqualTo(array($classFile => array(5 => 1, 6 => 2, 7 => 3, 8 => 2, 9 => 1)))
			->object($coverage->addxdebugData($this, $xdebugData))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEqualTo(array($classFile => array(5 => 2, 6 => 4, 7 => 6, 8 => 4, 9 => 2)))
		;
	}

	public function testMerge()
	{
		$mockController = new mock\controller();
		$mockController->__construct = function() {};
		$mockController->getName = function() {};
		$mockController->getFileName = function() use (& $classFile) { return $classFile; };

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\reflectionClass');

		$classFile = uniqid();

		$xdebugData = array(
		  $classFile =>
			 array(
				5 => 1,
				6 => 2,
				7 => 3,
				8 => 2,
				9 => 1
			),
		  uniqid() =>
			 array(
				5 => 2,
				6 => 3,
				7 => 4,
				8 => 3,
				9 => 2
			)
		);

		$coverage = new score\coverage();
		$coverage
			->setReflectionClassInjector(function($class) use ($mockController) { return new mock\reflectionClass($class, $mockController); })
			->addxdebugData($this, $xdebugData)
		;

		$otherCoverage = new score\coverage();
		$otherCoverage
			->setReflectionClassInjector(function($class) use ($mockController) { return new mock\reflectionClass($class, $mockController); })
			->addxdebugData($this, $xdebugData)
		;

		$this->assert
			->object($coverage->merge($otherCoverage))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEqualTo(array($classFile => array(5 => 2, 6 => 4, 7 => 6, 8 => 4, 9 => 2)))
		;
	}
}

?>
