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
			->object($coverage)->isInstanceOf('\countable')
			->array($coverage->getLines())->isEmpty()
			->array($coverage->getMethods())->isEmpty()
		;
	}

	public function testAddXdebugData()
	{
		$coverage = new score\coverage();

		$this->assert
			->object($coverage->addxdebugData($this, array()))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEmpty()
		;

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\reflectionClass')
			->generate('\reflectionMethod')
		;

		$methodController = new mock\controller();
		$methodController->__construct = function() {};
		$methodController->isAbstract = false;
		$methodController->getName = function() use (& $methodName) { return $methodName; };
		$methodController->getFileName = function() use (& $classFile) { return $classFile; };
		$methodController->getName = function() use (& $methodName) { return $methodName; };
		$methodController->getStartLine = 6;
		$methodController->getEndLine = 8;

		$classController = new mock\controller();
		$classController->__construct = function() {};
		$classController->getName = function() use (& $className) { return $className; };
		$classController->getFileName = function() use (& $classFile) { return $classFile; };
		$classController->getMethods = array(new mock\reflectionMethod(uniqid(), uniqid(), $methodController));

		$coverage->setReflectionClassInjector(function($class) use ($classController) { return new mock\reflectionClass($class, $classController); });

		$classFile = uniqid();
		$className = uniqid();
		$methodName = uniqid();

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
			->array($coverage->getLines())->isEqualTo(array(
					$classFile => array(
						6 => 2,
						7 => 3,
						8 => 2
					)
				)
			)
			->array($coverage->getMethods())->isEqualTo(array(
					$classFile => array(
						$className => array(
							$methodName => array(
								6 => 2,
								7 => 3,
								8 => 2
							)
						)
					)
				)
			)
			->array($coverage->getMethods())->isEqualTo(array(
					$classFile => array(
						$className => array(
							$methodName => array(
								6 => 2,
								7 => 3,
								8 => 2
							)
						)
					)
				)
			)
			->object($coverage->addxdebugData($this, $xdebugData))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEqualTo(array(
					$classFile => array(
						6 => 4,
						7 => 6,
						8 => 4
					)
				)
			)
			->array($coverage->getMethods())->isEqualTo(array(
					$classFile => array(
						$className => array(
							$methodName => array(
								6 => 4,
								7 => 6,
								8 => 4
							)
						)
					)
				)
			)
		;
	}

	public function testReset()
	{
		$coverage = new score\coverage();

		$this->assert
			->array($coverage->getLines())->isEmpty()
			->array($coverage->getMethods())->isEmpty()
			->object($coverage->reset())->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEmpty()
			->array($coverage->getMethods())->isEmpty()
		;

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\reflectionClass')
			->generate('\reflectionMethod')
		;

		$methodController = new mock\controller();
		$methodController->__construct = function() {};
		$methodController->getName = function() use (& $methodName) { return $methodName; };
		$methodController->isAbstract = false;
		$methodController->getFileName = function() use (& $classFile) { return $classFile; };
		$methodController->getStartLine = 6;
		$methodController->getEndLine = 8;

		$classController = new mock\controller();
		$classController->__construct = function() {};
		$classController->getName = function() use (& $className) { return $className; };
		$classController->getFileName = function() use (& $classFile) { return $classFile; };
		$classController->getMethods = array(new mock\reflectionMethod(uniqid(), uniqid(), $methodController));

		$classFile = uniqid();
		$className = uniqid();
		$methodName = uniqid();

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

		$coverage->addXdebugData($this, $xdebugData);

		$this->assert
			->array($coverage->getLines())->isNotEmpty()
			->array($coverage->getMethods())->isNotEmpty()
			->object($coverage->reset())->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEmpty()
			->array($coverage->getMethods())->isEmpty()
		;
	}

	public function testMerge()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\reflectionClass')
			->generate('\reflectionMethod')
		;

		$methodController = new mock\controller();
		$methodController->__construct = function() {};
		$methodController->getName = function() use (& $methodName) { return $methodName; };
		$methodController->isAbstract = false;
		$methodController->getFileName = function() use (& $classFile) { return $classFile; };
		$methodController->getStartLine = 6;
		$methodController->getEndLine = 8;

		$classController = new mock\controller();
		$classController->__construct = function() {};
		$classController->getName = function() use (& $className) { return $className; };
		$classController->getFileName = function() use (& $classFile) { return $classFile; };
		$classController->getMethods = array(new mock\reflectionMethod(uniqid(), uniqid(), $methodController));

		$classFile = uniqid();
		$className = uniqid();
		$methodName = uniqid();

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
			->setReflectionClassInjector(function($class) use ($classController) { return new mock\reflectionClass($class, $classController); })
		;

		$this->assert
			->object($coverage->merge($coverage))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEmpty()
			->array($coverage->getMethods())->isEmpty()
		;

		$otherCoverage = new score\coverage();

		$this->assert
			->object($coverage->merge($otherCoverage))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEmpty()
			->array($coverage->getMethods())->isEmpty()
		;

		$coverage->addxdebugData($this, $xdebugData);

		$this->assert
			->object($coverage->merge($otherCoverage))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEqualTo(array(
					$classFile => array(
						6 => 2,
						7 => 3,
						8 => 2
					)
				)
			)
			->array($coverage->getMethods())->isEqualTo(array(
					$classFile => array(
						$className => array(
							$methodName => array(
								6 => 2,
								7 => 3,
								8 => 2
							)
						)
					)
				)
			)
		;

		$this->assert
			->object($coverage->merge($coverage))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEqualTo(array(
					$classFile => array(
						6 => 4,
						7 => 6,
						8 => 4
					)
				)
			)
			->array($coverage->getMethods())->isEqualTo(array(
					$classFile => array(
						$className => array(
							$methodName => array(
								6 => 4,
								7 => 6,
								8 => 4
							)
						)
					)
				)
			)
		;

		$otherMethodController = new mock\controller();
		$otherMethodController->__construct = function() {};
		$otherMethodController->getName = function() use (& $otherMethodName) { return $otherMethodName; };
		$otherMethodController->isAbstract = false;
		$otherMethodController->getFileName = function() use (& $otherClassFile) { return $otherClassFile; };
		$otherMethodController->getStartLine = 5;
		$otherMethodController->getEndLine = 9;

		$otherClassController = new mock\controller();
		$otherClassController->__construct = function() {};
		$otherClassController->getName = function() use (& $otherClassName) { return $otherClassName; };
		$otherClassController->getFileName = function() use (& $otherClassFile) { return $otherClassFile; };
		$otherClassController->getMethods = array(new mock\reflectionMethod(uniqid(), uniqid(), $otherMethodController));

		$otherClassFile = uniqid();
		$otherClassName = uniqid();
		$otherMethodName = uniqid();

		$otherXdebugData = array(
		  $otherClassFile =>
			 array(
				1 => 10,
				2 => 20,
				3 => 30,
				4 => 40,
				5 => 50,
				6 => 60,
				7 => 70,
				8 => 80,
				9 => 90,
				10 => 100
			),
		  uniqid() =>
			 array(
				500 => 200,
				600 => 300,
				700 => 400,
				800 => 300,
				900 => 200
			)
		);

		$otherCoverage
			->setReflectionClassInjector(function($class) use ($otherClassController) { return new mock\reflectionClass($class, $otherClassController); })
		;

		$this->assert
			->object($coverage->merge($otherCoverage->addXdebugData($this, $otherXdebugData)))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEqualTo(array(
					$classFile => array(
						6 => 4,
						7 => 6,
						8 => 4
					),
					$otherClassFile => array(
						5 => 50,
						6 => 60,
						7 => 70,
						8 => 80,
						9 => 90
					)
				)
			)
			->array($coverage->getMethods())->isEqualTo(array(
					$classFile => array(
						$className => array(
							$methodName => array(
								6 => 4,
								7 => 6,
								8 => 4
							)
						)
					),
					$otherClassFile => array(
						$otherClassName => array(
							$otherMethodName => array(
								5 => 50,
								6 => 60,
								7 => 70,
								8 => 80,
								9 => 90
							)
						)
					)
				)
			)
		;
	}

	public function testCount()
	{
		$coverage = new score\coverage();

		$this->assert
			->sizeof($coverage)->isZero()
		;

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\reflectionClass')
			->generate('\reflectionMethod')
		;

		$methodController = new mock\controller();
		$methodController->__construct = function() {};
		$methodController->getName = function() use (& $methodName) { return $methodName; };
		$methodController->isAbstract = false;
		$methodController->getFileName = function() use (& $classFile) { return $classFile; };
		$methodController->getStartLine = 6;
		$methodController->getEndLine = 8;

		$classController = new mock\controller();
		$classController->__construct = function() {};
		$classController->getName = function() use (& $className) { return $className; };
		$classController->getFileName = function() use (& $classFile) { return $classFile; };
		$classController->getMethods = array(new mock\reflectionMethod(uniqid(), uniqid(), $methodController));

		$classFile = uniqid();
		$className = uniqid();
		$methodName = uniqid();

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
			->sizeof($coverage->addXdebugData($this, $xdebugData))->isEqualTo(1)
		;
	}

	public function testGetValue()
	{
		$coverage = new score\coverage();

		$this->assert
			->variable($coverage->getValue())->isNull()
		;

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\reflectionClass')
			->generate('\reflectionMethod')
		;

		$methodController = new mock\controller();
		$methodController->__construct = function() {};
		$methodController->getName = function() { return uniqid(); };
		$methodController->isAbstract = false;
		$methodController->getFileName = function() use (& $classFile) { return $classFile; };
		$methodController->getStartLine = 4;
		$methodController->getEndLine = 8;

		$classController = new mock\controller();
		$classController->__construct = function() {};
		$classController->getName = function() use (& $className) { return $className; };
		$classController->getFileName = function() use (& $classFile) { return $classFile; };
		$classController->getMethods = array(new mock\reflectionMethod(uniqid(), uniqid(), $methodController));

		$classFile = uniqid();
		$className = uniqid();

		$xdebugData = array(
		  $classFile =>
			 array(
				3 => 0,
				4 => 0,
				5 => 0,
				6 => 0,
				7 => 0,
				8 => 0,
				9 => 0
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

		$coverage
			->setReflectionClassInjector(function($class) use ($classController) { return new mock\reflectionClass($class, $classController); })
		;

		$coverage->addXdebugData($this, $xdebugData);

		$this->assert
			->float($coverage->getValue())->isEqualTo(0.0)
		;

		$xdebugData = array(
		  $classFile =>
			 array(
				3 => 0,
				4 => 0,
				5 => 0,
				6 => 0,
				7 => 0,
				8 => 1,
				9 => 0
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

		$coverage->reset()->addXdebugData($this, $xdebugData);

		$this->assert
			->float($coverage->getValue())->isEqualTo(1 / 5)
		;

		$xdebugData = array(
		  $classFile =>
			 array(
				3 => 0,
				4 => 0,
				5 => 0,
				6 => 0,
				7 => 2,
				8 => 1,
				9 => 0
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

		$coverage->reset()->addXdebugData($this, $xdebugData);

		$this->assert
			->float($coverage->getValue())->isEqualTo(2 / 5)
		;

		$xdebugData = array(
		  $classFile =>
			 array(
				3 => 0,
				4 => 5,
				5 => 4,
				6 => 3,
				7 => 2,
				8 => 1,
				9 => 0
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

		$coverage->reset()->addXdebugData($this, $xdebugData);

		$this->assert
			->float($coverage->getValue())->isEqualTo(1.0)
		;
	}
}

?>
